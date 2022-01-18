<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Contao\FormFieldModel;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FormFieldMapper;
use Netzmacht\ContaoFormBundle\Form\HtmlType;

final class ExplanationFieldMapper implements FormFieldMapper
{
    public function supports(FormFieldModel $model): bool
    {
        return $model->type === 'explanation';
    }

    public function getName(FormFieldModel $model): ?string
    {
        return 'field_' . $model->id;
    }

    public function getTypeClass(FormFieldModel $model): string
    {
        return HtmlType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $fieldTypeBuilder, callable $next): array
    {
        return [
            'html'  => $model->text,
            'class' => $model->class ?: 'tl_' . $model->type,
        ];
    }
}
