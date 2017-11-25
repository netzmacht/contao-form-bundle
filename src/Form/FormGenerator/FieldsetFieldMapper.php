<?php

/**
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved
 * @filesource
 *
 */

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator;

use AdamQuaile\Bundle\FieldsetBundle\Form\FieldsetType;
use Contao\FormFieldModel;

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
     * {@inheritdoc}
     */
    public function getTypeClass(FormFieldModel $model): string
    {
        return FieldsetType::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $typeBuilder, callable $next): array
    {
        $options = [
            'label'  => false,
            'legend' => $model->label,
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
            $options['fields'][] = $typeBuilder->build($child, $next);
        }

        return $options;
    }
}
