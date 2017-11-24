<?php

/**
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved
 * @filesource
 *
 */

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator;

use Contao\FormFieldModel;

/**
 * Class FieldBuilder
 *
 * @package Netzmacht\ContaoFormBundle\Form\FormGenerator
 */
class FieldTypeBuilder
{
    /**
     * Form field mappers.
     *
     * @var FormFieldMapper[]
     */
    private $mappers;

    /**
     * Build the form field type.
     *
     * @param FormFieldModel $fieldModel
     *
     * @return array|null
     */
    public function build(FormFieldModel $fieldModel): ?array
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->supports($fieldModel)) {
                $config = [
                    'name'     => $fieldModel->name,
                    'type'     => $mapper->getTypeClass($fieldModel),
                    'options'  => $mapper->getOptions($fieldModel),
                    'children' => [],
                    'group'    => null
                ];

                if ($mapper instanceof GroupFormFieldMapper) {
                    $config['group'] = $mapper->isGroupOpener($fieldModel) ? 'start' : 'stop';
                }

                return $config;
            }
        }

        return null;
    }

    public function setChildrenAsOption(FormFieldModel $fieldModel, array $config): array
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->supports($fieldModel) && $mapper instanceof GroupFormFieldMapper) {
                return $mapper->setChildrenAsOption($config['options'], $config['children']);
            }
        }

        return $config['options'];
    }
}
