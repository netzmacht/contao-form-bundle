<?php

/**
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved
 * @filesource
 *
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator;

use Contao\FormFieldModel;

/**
 * Interface GroupFormFieldMapper
 *
 * @package Netzmacht\ContaoFormBundle\Form\FormGenerator
 */
interface GroupFormFieldMapper extends FormFieldMapper
{
    /**
     * Returns true if element starts a group.
     *
     * @param FormFieldModel $model The form model.
     *
     * @return bool
     */
    public function isGroupOpener(FormFieldModel $model): bool;

    /**
     * Returns true if elements stops a group.
     *
     * @param FormFieldModel $model The form model.
     *
     * @return bool
     */
    public function isGroupEnd(FormFieldModel $model): bool;

    /**
     * Get the options for the form field.
     *
     * @param array $options  Form type options.
     * @param array $children Optional form field configs as array.
     *
     * @return array
     */
    public function setChildrenAsOption(array $options, array $children = []): array;
}
