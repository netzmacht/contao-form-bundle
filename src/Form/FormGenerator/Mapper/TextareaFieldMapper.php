<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Contao\FormFieldModel;
use Contao\StringUtil;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class TextareaFieldMapper extends AbstractFieldMapper
{
    /**
     * The form field type.
     */
    protected string $fieldType = 'textarea';

    /**
     * The type class.
     */
    protected string $typeClass = TextareaType::class;

    /**
     * {@inheritDoc}
     */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $fieldTypeBuilder, callable $next): array
    {
        $options = parent::getOptions($model, $fieldTypeBuilder, $next);
        $size    = StringUtil::deserialize($model->size);

        if ($size[0] !== '') {
            $options['attr']['rows'] = $size[0];
        }

        if ($size[1] !== '') {
            $options['attr']['cols'] = $size[1];
        }

        return $options;
    }
}
