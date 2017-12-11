<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
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
     * FieldTypeBuilder constructor.
     *
     * @param FormFieldMapper[] $mappers Form field mappers.
     */
    public function __construct(array $mappers)
    {
        $this->mappers = $mappers;
    }

    /**
     * Build the form field type.
     *
     * @param FormFieldModel $fieldModel The field model.
     * @param callable       $next       Callback to get the next form field model.
     *
     * @return array|null
     */
    public function build(FormFieldModel $fieldModel, $next): ?array
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->supports($fieldModel)) {
                return [
                    'name'    => $mapper->getName($fieldModel),
                    'type'    => $mapper->getTypeClass($fieldModel),
                    'options' => $mapper->getOptions($fieldModel, $this, $next)
                ];
            }
        }

        return null;
    }
}
