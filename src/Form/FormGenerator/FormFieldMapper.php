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
     * Get the name of the form field.
     *
     * @param FormFieldModel $model The form field.
     *
     * @return string|null
     */
    public function getName(FormFieldModel $model): ?string;

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
     * @param FormFieldModel   $model            The form field.
     * @param FieldTypeBuilder $fieldTypeBuilder The field type builder.
     * @param callable         $next             Callable to fetch the next element.
     *
     * @return array
     */
    public function getOptions(
        FormFieldModel $model,
        FieldTypeBuilder $fieldTypeBuilder,
        callable $next
    ): array;
}
