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

namespace Netzmacht\ContaoFormBundle\Filter;

use Netzmacht\Contao\Toolkit\Dca\Definition;

/**
 * Input filter based on data container.
 */
final class DataContainerBasedInputFilter
{
    /**
     * The input filter.
     *
     * @var ContaoInputFilter
     */
    private $inputFilter;

    /**
     * Constructor.
     *
     * @param ContaoInputFilter $inputFilter The input filter.
     */
    public function __construct(ContaoInputFilter $inputFilter)
    {
        $this->inputFilter = $inputFilter;
    }

    /**
     * Filter the given input.
     *
     * @param Definition $definition The data container definition.
     * @param array      $data       The given data.
     *
     * @return array
     */
    public function filter(Definition $definition, array $data): array
    {
        foreach ($data as $key => $value) {
            $data[$key] = $this->filterValue($definition, $key, $value);
        }

        return $data;
    }

    /**
     * Filter a given value for a specific field.
     *
     * @param Definition $definition The data container definition.
     * @param string     $field      The field name.
     * @param mixed      $value      The given value.
     *
     * @return mixed
     */
    public function filterValue(Definition $definition, string $field, $value)
    {
        if ($this->useRawRequestData($definition, $field)) {
            return $value;
        }

        if ($this->getEvaluationSetting($definition, $field, 'preserveTags')) {
            return $this->inputFilter->filterRaw($value);
        }

        $decodeEntities = $this->getEvaluationSetting($definition, $field, 'decodeEntities', false);
        $allowHtml      = $this->getEvaluationSetting($definition, $field, 'allowHtml', false);

        return $this->inputFilter->filter($value, $decodeEntities, $allowHtml);
    }

    /**
     * Determine if the field uses raw request data.
     *
     * @param Definition $definition The data container definition.
     * @param string     $field      The field name.
     *
     * @return bool
     */
    private function useRawRequestData(Definition $definition, string $field) : bool
    {
        if ($definition->get(['config', 'useRawRequestData'])) {
            return true;
        }

        return (bool) $this->getEvaluationSetting($definition, $field, 'useRawRequestData');
    }

    /**
     * Get an evaluation configuration setting for a field.
     *
     * @param Definition $definition The data container definition.
     * @param string     $field      The field name.
     * @param string     $setting    The name of the field.
     * @param mixed      $default    The default value used if setting is not defined.
     *
     * @return mixed
     */
    private function getEvaluationSetting(Definition $definition, string $field, string $setting, $default = null)
    {
        return $definition->get(['fields', $field, 'eval', $setting], $default);
    }
}
