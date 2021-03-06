<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use AdamQuaile\Bundle\FieldsetBundle\Form\FieldsetType;
use Contao\FormFieldModel;
use Contao\StringUtil;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FormFieldMapper;

/**
 * Class FieldsetFieldMapper
 */
class FieldsetFieldMapper implements FormFieldMapper
{
    /**
     * {@inheritDoc}
     */
    public function supports(FormFieldModel $model): bool
    {
        if ($model->type === 'fieldsetStart') {
            return true;
        }

        // Fallback for Contao 4.4
        return ($model->type === 'fieldset' && $model->fsType === 'fsStart');
    }

    /**
     * {@inheritDoc}
     */
    public function getName(FormFieldModel $model): ?string
    {
        return 'fieldset_' . $model->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeClass(FormFieldModel $model): string
    {
        return FieldsetType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $typeBuilder, callable $next): array
    {
        $options = [
            'label'  => false,
            'legend' => StringUtil::decodeEntities($model->label),
            'attr'   => [],
            'fields' => []
        ];

        if ($model->class) {
            $options['attr']['class'] = $model->class;
        }

        $condition = function (FormFieldModel $model) {
            if ($model->type === 'fieldsetStop') {
                return false;
            }

            if ($model->type === 'fieldset' && $model->fsType === 'fsStop') {
                return false;
            }

            return true;
        };

        while (($child = $next($condition)) !== null) {
            $field = $typeBuilder->build($child, $next);

            if ($field) {
                $options['fields'][] = [
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'attr' => $field['options'],
                ];
            }
        }

        return $options;
    }
}
