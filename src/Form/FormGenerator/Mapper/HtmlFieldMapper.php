<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Contao\FormFieldModel;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FormFieldMapper;
use Netzmacht\ContaoFormBundle\Form\HtmlType;

final class HtmlFieldMapper implements FormFieldMapper
{
    public function supports(FormFieldModel $model): bool
    {
        return $model->type === 'html';
    }

    public function getName(FormFieldModel $model): string|null
    {
        return 'field_' . $model->id;
    }

    public function getTypeClass(FormFieldModel $model): string
    {
        return HtmlType::class;
    }

    /** {@inheritDoc} */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $fieldTypeBuilder, callable $next): array
    {
        return [
            'html'  => $model->html,
            'class' => $model->class ?: 'tl_' . $model->type,
        ];
    }
}
