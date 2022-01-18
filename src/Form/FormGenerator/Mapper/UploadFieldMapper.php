<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Assert\AssertionFailedException;
use Contao\CoreBundle\Framework\Adapter;
use Contao\FormFieldModel;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;

use function explode;
use function min;

/**
 * Class UploadFieldMapper maps the upload form field to the FileType
 */
final class UploadFieldMapper extends AbstractFieldMapper
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $fieldType = 'upload';

    /**
     * The type class.
     *
     * @var string
     */
    protected $typeClass = FileType::class;

    /**
     * Config adapter.
     *
     * @var Adapter
     */
    private $configAdapter;

    /**
     * Construct.
     *
     * @param Adapter $configAdapter Config adapter.
     *
     * @throws AssertionFailedException When mapper is misconfigured.
     */
    public function __construct($configAdapter)
    {
        parent::__construct();

        $this->options['minlength'] = false;
        $this->options['maxlength'] = false;
        $this->options['value']     = false;

        $this->configAdapter = $configAdapter;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $fieldTypeBuilder, callable $next): array
    {
        $options                  = parent::getOptions($model, $fieldTypeBuilder, $next);
        $options['constraints'][] = [
            new File($this->buildConstraintOptions($model)),
        ];

        return $options;
    }

    /**
     * Build the file constraints options.
     *
     * @param FormFieldModel $model The form field model.
     *
     * @return array<string,mixed>
     */
    private function buildConstraintOptions(FormFieldModel $model): array
    {
        if ($model->maxlength > 0) {
            $maxSize = $model->maxlength;
        } else {
            $maxSize = min(UploadedFile::getMaxFilesize(), (int) $this->configAdapter->__call('get', ['maxFileSize']));
        }

        $options = ['maxSize' => $maxSize];

        if ($model->extensions) {
            $options['mimeTypes'] = $this->getSupportedMimeTypes($model->extensions);
        }

        return $options;
    }

    /**
     * Get a list of all supported mime types.
     *
     * @param string $extensions Allowed file extensions as csv value.
     *
     * @return list<string>
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getSupportedMimeTypes(string $extensions): array
    {
        $mimeTypes = [];

        foreach (explode(',', $extensions) as $extension) {
            if (! isset($GLOBALS['TL_MIME'][$extension])) {
                continue;
            }

            $mimeTypes[] = $GLOBALS['TL_MIME'][$extension];
        }

        return $mimeTypes;
    }
}
