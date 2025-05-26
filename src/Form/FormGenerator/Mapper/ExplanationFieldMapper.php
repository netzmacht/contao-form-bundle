<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Contao\FormFieldModel;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FormFieldMapper;
use Netzmacht\ContaoFormBundle\Form\HtmlType;
use Override;

final class ExplanationFieldMapper implements FormFieldMapper
{
    #[Override]
    public function supports(FormFieldModel $model): bool
    {
        return $model->type === 'explanation';
    }

    #[Override]
    public function getName(FormFieldModel $model): string|null
    {
        return 'field_' . $model->id;
    }

    #[Override]
    public function getTypeClass(FormFieldModel $model): string
    {
        return HtmlType::class;
    }

    /** {@inheritDoc} */
    #[Override]
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $fieldTypeBuilder, callable $next): array
    {
        return [
            'html'  => $model->text,
            'class' => $model->class ?: 'tl_' . $model->type,
        ];
    }
}
