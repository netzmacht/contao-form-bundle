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

namespace Netzmacht\ContaoFormBundle\Form\DcaForm;

use Netzmacht\Contao\Toolkit\Dca\Definition;

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
     * @param string     $name     Form field name.
     * @param array      $config     Form field config.
     * @param Definition $definition Data container definition.
     * @param callable   $next       Callback to get the next form field model.
     *
     * @return array|null
     */
    public function build(string $name, array $config, Definition $definition, callable $next): ?array
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->supports($name, $config)) {
                return [
                    'name'    => $name,
                    'type'    => $mapper->getTypeClass($name, $config),
                    'options' => $mapper->getOptions($name, $config, $definition, $this, $next)
                ];
            }
        }

        return null;
    }
}
