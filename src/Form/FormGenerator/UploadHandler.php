<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2019 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Dbafs;
use Contao\FilesModel;
use Contao\FormFieldModel;
use Contao\FrontendUser;
use Contao\Model;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper\UploadFieldMapper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\PathUtil\Path;
use function basename;
use function defined;
use function file_exists;
use function umask;
use const TL_FILES;

/**
 * Class UploadHandler
 */
final class UploadHandler
{
    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * The upload field mapper.
     *
     * @var UploadFieldMapper
     */
    private $uploadFieldMapper;

    /**
     * The file system.
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Contao framework.
     *
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The project root dir.
     *
     * @var string
     */
    private $projectDir;

    /**
     * UploadHandler constructor.
     *
     * @param RepositoryManager        $repositoryManager Repository manager.
     * @param UploadFieldMapper        $uploadFieldMapper Upload field mapper.
     * @param Filesystem               $filesystem        Filesystem.
     * @param ContaoFrameworkInterface $framework         Contao framework.
     * @param LoggerInterface          $logger            Logger.
     * @param string                   $projectDir        Project root dir.
     */
    public function __construct(
        RepositoryManager $repositoryManager,
        UploadFieldMapper $uploadFieldMapper,
        Filesystem $filesystem,
        ContaoFrameworkInterface $framework,
        LoggerInterface $logger,
        string $projectDir
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->uploadFieldMapper = $uploadFieldMapper;
        $this->filesystem        = $filesystem;
        $this->framework         = $framework;
        $this->logger            = $logger;
        $this->projectDir        = $projectDir;
    }

    /**
     * Handle file upload for a single form field.
     *
     * Returns an array containing keys path and model. Model may be null if not synced with the file system.
     *
     * @param FormFieldModel $fieldModel The form field model.
     * @param UploadedFile   $file       The uploaded file.
     *
     * @return array|null
     *
     * @throws \Exception When upload folder does not exists in the dbafs.
     */
    public function handleFormField(FormFieldModel $fieldModel, UploadedFile $file): ?array
    {
        if (!$fieldModel->storeFile) {
            return null;
        }

        $uploadFolderPath = $this->getUploadFolder($fieldModel);
        $uploadFolder     = $this->projectDir . '/' . $uploadFolderPath;

        // Only store if folder exists. This is the same behaviour like the Contao upload form element has.
        if (!$this->filesystem->exists($uploadFolder)) {
            return null;
        }

        $fileName = $this->getFileName($fieldModel, $file, $uploadFolder);
        $target   = $file->move($uploadFolder, $fileName);

        if ($target->getRealPath() === false) {
            throw new \Exception('Target does not exist.');
        }

        $this->filesystem->chmod($target->getRealPath(), 0666, umask());

        /** @var Dbafs&Adapter $dbafs */
        $dbafs     = $this->framework->getAdapter(Dbafs::class);
        $filePath  = Path::makeRelative($target->getRealPath(), $this->projectDir);
        $fileModel = null;

        if ($dbafs->__call('shouldBeSynchronized', [$target->getPath()])) {
            /** @var ContaoRepository $repository */
            $repository = $this->repositoryManager->getRepository(FilesModel::class);
            $fileModel  = $repository->__call('findByPath', [$filePath]);

            if ($fileModel === null) {
                $fileModel = $dbafs->__call('addResource', [$filePath]);
            }

            $dbafs->__call('updateFolderHashes', [$uploadFolderPath]);
        }

        $this->logger->info(
            sprintf('File "%s" has been uploaded', $filePath),
            ['contao' => new ContaoContext(__METHOD__, TL_FILES)]
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
     * @param int   $formId The form id.
     * @param array $data   The form data as array.
     *
     * @return array
     *
     * @throws \Exception When upload folder does not exists in the dbafs.
     */
    public function handleForm(int $formId, array $data): array
    {
        /** @var FormFieldModel $fieldModel */
        foreach ($this->loadUploadFormFields($formId) as $fieldModel) {
            if (!$this->uploadFieldMapper->supports($fieldModel)) {
                continue;
            }

            $name  = $this->uploadFieldMapper->getName($fieldModel);
            $value = ($data[$name] ?? null);

            if (!$value instanceof UploadedFile) {
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
            ['order' => '.sorting ASC']
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
     * @return string
     *
     * @throws \Exception When upload folder does not exists in the dbafs.
     */
    private function getUploadFolder(FormFieldModel $fieldModel): string
    {
        $uploadFolderUuid = $fieldModel->uploadFolder;

        // Overwrite the upload folder with user's home directory
        if ($fieldModel->useHomeDir && defined('FE_USER_LOGGED_IN') && FE_USER_LOGGED_IN) {
            /** @var FrontendUser $user */
            $user = $this->framework->createInstance(FrontendUser::class);
            if ($user->assignDir && $user->homeDir) {
                $uploadFolderUuid = $user->homeDir;
            }
        }

        /** @var ContaoRepository $repository */
        $repository   = $this->repositoryManager->getRepository(FilesModel::class);
        $uploadFolder = $repository->__call('findByUuid', [$uploadFolderUuid]);

        // The upload folder could not be found
        if ($uploadFolder === null) {
            throw new \Exception(sprintf('Invalid upload folder ID %s', $uploadFolder));
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
     * @return string
     *
     * @throws \Exception If no original client name is given.
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
                scan($uploadFolder)
            );

            foreach ($files as $file) {
                if (preg_match('/__[0-9]+\.' . preg_quote($extension, '/') . '$/', $file)) {
                    $file   = str_replace('.' . $extension, '', $file);
                    $value  = (int) substr($file, (strrpos($file, '_') + 1));
                    $offset = max($offset, $value);
                }
            }

            $fileName = str_replace($baseName, $baseName . '__' . (++$offset), $fileName);
        }

        return $fileName;
    }
}
