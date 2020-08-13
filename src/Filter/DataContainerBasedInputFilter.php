<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Filter;

use Netzmacht\Contao\Toolkit\Dca\Definition;

/**
 * Input filter based on data container.
 */
final class DataContainerBasedInputFilter
{
    /** @var ContaoInputFilter */
    private $inputFilter;

    /**
     * Constructor.
     *
     * @param ContaoInputFilter $inputFilter
     */
    public function __construct(ContaoInputFilter $inputFilter)
    {
        $this->inputFilter = $inputFilter;
    }

    public function filter(Definition $definition, array $data): array
    {
        foreach ($data as $key => $value) {
            $data[$key] = $this->filterValue($definition, $key, $value);
        }

        return $data;
    }

    private function filterValue(Definition $definition, $key, $value)
    {
        if ($this->useRawRequestData($definition, $key)) {
            return $value;
        }

        if ($this->getEvaluationSetting($definition, $key, 'preserveTags')) {
            return $this->inputFilter->filterRaw($value);
        }

        $decodeEntities = $this->getEvaluationSetting($definition, $key, 'decodeEntities', false);
        $allowHtml      = $this->getEvaluationSetting($definition, $key, 'allowHtml', false);

        return $this->inputFilter->filter($value, $decodeEntities, $allowHtml);
    }

    private function useRawRequestData(Definition $definition, $offset) : bool
    {
        if ($definition->get(['config', 'useRawRequestData'])) {
            return true;
        }

        return (bool) $this->getEvaluationSetting($definition, $offset, 'useRawRequestData');
    }

    private function getEvaluationSetting(Definition $definition, $offset, string $setting, $default = null)
    {
        return $definition->get(['fields', $offset, 'eval', $setting], $default);
    }
}
