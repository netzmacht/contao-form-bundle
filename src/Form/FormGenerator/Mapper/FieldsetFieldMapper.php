<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Contao\FormFieldModel;
use Contao\StringUtil;
use Netzmacht\ContaoFormBundle\Form\FieldsetType;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FormFieldMapper;

final class FieldsetFieldMapper implements FormFieldMapper
{
    public function supports(FormFieldModel $model): bool
    {
        if ($model->type === 'fieldsetStart') {
            return true;
        }

        // Fallback for Contao 4.4
        return $model->type === 'fieldset' && $model->fsType === 'fsStart';
    }

    public function getName(FormFieldModel $model): ?string
    {
        return 'fieldset_' . $model->id;
    }

    public function getTypeClass(FormFieldModel $model): string
    {
        return FieldsetType::class;
    }

    /**
     * {@inheritDoc}
     */
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

            if (! $field) {
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
