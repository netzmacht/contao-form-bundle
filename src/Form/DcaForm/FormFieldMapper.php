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

namespace Netzmacht\ContaoFormBundle\Form\DcaForm;

use Netzmacht\Contao\Toolkit\Dca\Definition;

/**
 * Interface FormTypeMapper
 */
interface FormFieldMapper
{
    /**
     * Check if type mapper supports the form field.
     *
     * @param string $name   The form field name.
     * @param array  $config The form field config.
     *
     * @return bool
     */
    public function supports(string $name, array $config): bool;

    /**
     * Get the type class from the form field.
     *
     * @param string $name   The form field name.
     * @param array  $config The form field config.
     *
     * @return string
     */
    public function getTypeClass(string $name, array $config): string;

    /**
     * Get the options for the form field.
     *
     * @param string           $name             The form field name.
     * @param array            $config           The form field config.
     * @param Definition       $definition       Data container definition.
     * @param FieldTypeBuilder $fieldTypeBuilder The field type builder.
     * @param callable         $next             Callable to fetch the next element.
     *
     * @return array
     */
    public function getOptions(
        string $name,
        array $config,
        Definition $definition,
        FieldTypeBuilder $fieldTypeBuilder,
        callable $next
    ): array;
}
