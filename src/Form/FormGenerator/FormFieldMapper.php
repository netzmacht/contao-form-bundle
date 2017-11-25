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
 * Interface FormTypeMapper
 */
interface FormFieldMapper
{
    /**
     * Check if type mapper supports the form field.
     *
     * @param FormFieldModel $model The form field.
     *
     * @return bool
     */
    public function supports(FormFieldModel $model): bool;

    /**
     * Get the type class from the form field.
     *
     * @param FormFieldModel $model The form field.
     *
     * @return string
     */
    public function getTypeClass(FormFieldModel $model): string;

    /**
     * Get the options for the form field.
     *
     * @param FormFieldModel   $model The form field.
     * @param FieldTypeBuilder $fieldTypeBuilder
     * @param callable         $next
     *
     * @return array
     */
    public function getOptions(
        FormFieldModel $model,
        FieldTypeBuilder $fieldTypeBuilder,
        callable $next
    ): array;
}
