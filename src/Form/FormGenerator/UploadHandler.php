<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\Dbafs;
use Contao\FilesModel;
use Contao\Folder;
use Contao\FormFieldModel;
use Contao\FrontendUser;
use Contao\Model;
use Contao\StringUtil;
use Contao\System;
use Exception;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper\UploadFieldMapper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\PathUtil\Path;

use function assert;
use function basename;
use function file_exists;
use function max;
use function preg_grep;
use function preg_match;
use function preg_quote;
use function sprintf;
use function str_replace;
use function strrpos;
use function substr;
use function umask;

final class UploadHandler
{
    private TokenChecker $tokenChecker;

    /**
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param UploadFieldMapper $uploadFieldMapper Upload field mapper.
     * @param Filesystem        $filesystem        Filesystem.
     * @param ContaoFramework   $framework         Contao framework.
     * @param LoggerInterface   $logger            Logger.
     * @param string            $projectDir        Project root dir.
     */
    public function __construct(
        private readonly RepositoryManager $repositoryManager,
        private readonly UploadFieldMapper $uploadFieldMapper,
        private readonly Filesystem $filesystem,
        private readonly ContaoFramework $framework,
        private readonly LoggerInterface $logger,
        private readonly string $projectDir,
        TokenChecker|null $tokenChecker = null,
    ) {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->tokenChecker = $tokenChecker ?? System::getContainer()->get('contao.security.token_checker');
    }

    /**
     * Handle file upload for a single form field.
     *
     * Returns an array containing keys path and model. Model may be null if not synced with the file system.
     *
     * @param FormFieldModel $fieldModel The form field model.
     * @param UploadedFile   $file       The uploaded file.
     *
     * @return array<string,string|FilesModel|null>|null
     *
     * @throws Exception When upload folder does not exist in the dbafs.
     */
    public function handleFormField(FormFieldModel $fieldModel, UploadedFile $file): array|null
    {
        if (! $fieldModel->storeFile) {
            return null;
        }

        $uploadFolderPath = $this->getUploadFolder($fieldModel);
        $uploadFolder     = $this->projectDir . '/' . $uploadFolderPath;

        // Only store if folder exists. This is the same behaviour like the Contao upload form element has.
        if (! $this->filesystem->exists($uploadFolder)) {
            return null;
        }

        $fileName = $this->getFileName($fieldModel, $file, $uploadFolder);
        $target   = $file->move($uploadFolder, $fileName);

        if ($target->getRealPath() === false) {
            throw new Exception('Target does not exist.');
        }

        $this->filesystem->chmod($target->getRealPath(), 0666, umask());

        $dbafs     = $this->framework->getAdapter(Dbafs::class);
        $filePath  = Path::makeRelative($target->getRealPath(), $this->projectDir);
        $fileModel = null;

        if ($dbafs->__call('shouldBeSynchronized', [$target->getPath()])) {
            $repository = $this->repositoryManager->getRepository(FilesModel::class);
            assert($repository instanceof ContaoRepository);
            $fileModel = $repository->__call('findByPath', [$filePath]);

            if ($fileModel === null) {
                $fileModel = $dbafs->__call('addResource', [$filePath]);
            }

            $dbafs->__call('updateFolderHashes', [$uploadFolderPath]);
        }

        $this->logger->info(
            sprintf('File "%s" has been uploaded', $filePath),
            ['contao' => new ContaoContext(__METHOD__, 'files')],
        );

        return [
            'path'  => $filePath,
            'model' => $fileModel,
        ];
    }

    /**
     * Handle all form fields for a given form id.
     *
     * Replace instances of UploadedFile with the related FilesModel.
     *
     * @param int                 $formId The form id.
     * @param array<string,mixed> $data   The form data as array.
     *
     * @return array<string,mixed>
     *
     * @throws Exception When upload folder does not exist in the dbafs.
     */
    public function handleForm(int $formId, array $data): array
    {
        foreach ($this->loadUploadFormFields($formId) as $fieldModel) {
            assert($fieldModel instanceof FormFieldModel);
            if (! $this->uploadFieldMapper->supports($fieldModel)) {
                continue;
            }

            $name  = (string) $this->uploadFieldMapper->getName($fieldModel);
            $value = ($data[$name] ?? null);

            if (! $value instanceof UploadedFile) {
                continue;
            }

            $data[$name] = $this->handleFormField($fieldModel, $value);
        }

        return $data;
    }

    /**
     * Find all form fields of the form.
     *
     * @param int $formId The form id.
     *
     * @return FormFieldModel[]|Model[]|array
     */
    private function loadUploadFormFields(int $formId): array
    {
        $repository = $this->repositoryManager->getRepository(FormFieldModel::class);
        $collection = $repository->findBy(
            ['.pid=?', '.invisible=?', 'type=?'],
            [$formId, '', 'upload'],
            ['order' => '.sorting ASC'],
        );

        if ($collection) {
            return $collection->getModels();
        }

        return [];
    }

    /**
     * Get the upload folder.
     *
     * @param FormFieldModel $fieldModel The form field model.
     *
     * @throws Exception When upload folder does not exist in the dbafs.
     */
    private function getUploadFolder(FormFieldModel $fieldModel): string
    {
        $uploadFolderUuid = $fieldModel->uploadFolder;

        // Overwrite the upload folder with user's home directory
        if ($fieldModel->useHomeDir && $this->tokenChecker->hasFrontendUser()) {
            $user = $this->framework->createInstance(FrontendUser::class);
            if ($user->assignDir && $user->homeDir !== '') {
                $uploadFolderUuid = $user->homeDir;
            }
        }

        $repository = $this->repositoryManager->getRepository(FilesModel::class);
        assert($repository instanceof ContaoRepository);
        $uploadFolder = $repository->__call('findByUuid', [$uploadFolderUuid]);

        // The upload folder could not be found
        if ($uploadFolder === null) {
            throw new Exception(sprintf('Invalid upload folder ID %s', StringUtil::binToUuid($uploadFolderUuid)));
        }

        return $uploadFolder->path;
    }

    /**
     * Get the filename for an uploaded file.
     *
     * @param FormFieldModel $fieldModel   The form field model.
     * @param UploadedFile   $uploadedFile The uploaded file.
     * @param string         $uploadFolder The upload folder as absolute path.
     *
     * @throws Exception If no original client name is given.
     */
    private function getFileName(FormFieldModel $fieldModel, UploadedFile $uploadedFile, string $uploadFolder): string
    {
        $fileName = $uploadedFile->getClientOriginalName() ?: $uploadedFile->getFilename();
        $fileName = StringUtil::sanitizeFileName($fileName);

        if ($fieldModel->doNotOverwrite && file_exists($uploadFolder . '/' . $fileName)) {
            $extension = $uploadedFile->getClientOriginalExtension();
            $baseName  = basename($fileName, '.' . $uploadedFile->getClientOriginalExtension());
            $offset    = 1;
            $files     = preg_grep(
                '/^' . preg_quote($baseName, '/') . '.*\.' . preg_quote($extension, '/') . '$/',
                Folder::scan($uploadFolder),
            );

            foreach ($files as $file) {
                if (! preg_match('/__[0-9]+\.' . preg_quote($extension, '/') . '$/', $file)) {
                    continue;
                }

                $file   = str_replace('.' . $extension, '', $file);
                $value  = (int) substr($file, (int) strrpos($file, '_') + 1);
                $offset = max($offset, $value);
            }

            $fileName = str_replace($baseName, $baseName . '__' . (++$offset), $fileName);
        }

        return $fileName;
    }
}
