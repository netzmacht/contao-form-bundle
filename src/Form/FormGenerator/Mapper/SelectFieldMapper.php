<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Contao\FormFieldModel;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Override;

final class SelectFieldMapper extends AbstractChoicesFieldMapper
{
    /**
     * The form field type.
     */
    protected string $fieldType = 'select';

    /**
     * Display the choices expanded.
     */
    protected bool $expanded = false;

    /** {@inheritDoc} */
    #[Override]
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $fieldTypeBuilder, callable $next): array
    {
        $options = parent::getOptions($model, $fieldTypeBuilder, $next);

        if ((bool) ($options['multiple'] ?? false) && $model->mSize > 0) {
            $options['attr']['size'] = $model->mSize;
        }

        return $options;
    }
}
