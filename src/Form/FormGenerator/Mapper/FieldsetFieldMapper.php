<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Contao\FormFieldModel;
use Contao\StringUtil;
use Netzmacht\ContaoFormBundle\Form\FieldsetType;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FormFieldMapper;
use Override;

final class FieldsetFieldMapper implements FormFieldMapper
{
    #[Override]
    public function supports(FormFieldModel $model): bool
    {
        if ($model->type === 'fieldsetStart') {
            return true;
        }

        // Fallback for Contao 4.4
        return $model->type === 'fieldset' && $model->fsType === 'fsStart';
    }

    #[Override]
    public function getName(FormFieldModel $model): string|null
    {
        return 'fieldset_' . $model->id;
    }

    #[Override]
    public function getTypeClass(FormFieldModel $model): string
    {
        return FieldsetType::class;
    }

    /** {@inheritDoc} */
    #[Override]
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $fieldTypeBuilder, callable $next): array
    {
        $options = [
            'label'  => false,
            'legend' => StringUtil::decodeEntities($model->label),
            'attr'   => [],
            'fields' => [],
        ];

        if ($model->class) {
            $options['attr']['class'] = $model->class;
        }

        $condition = static function (FormFieldModel $model): bool {
            if ($model->type === 'fieldsetStop') {
                return false;
            }

            return $model->type !== 'fieldset' || $model->fsType !== 'fsStop';
        };

        while (($child = $next($condition)) !== null) {
            $field = $fieldTypeBuilder->build($child, $next);

            if ($field === null) {
                continue;
            }

            $options['fields'][] = [
                'name' => $field['name'],
                'type' => $field['type'],
                'attr' => $field['options'],
            ];
        }

        return $options;
    }
}
