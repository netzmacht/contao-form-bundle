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

namespace Netzmacht\ContaoFormBundle\Form;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\ContaoFormBundle\Form\DcaForm\WidgetTypeBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DcaFormType
 */
class DcaFormType extends AbstractType
{
    /**
     * Data container manager.
     *
     * @var Manager
     */
    private $dcaManager;

    /**
     * @var WidgetTypeBuilder
     */
    private $typeBuilder;

    /**
     * DcaFormType constructor.
     *
     * @param Manager           $dcaManager  Data container manager.
     * @param WidgetTypeBuilder $typeBuilder Field type builder.
     */
    public function __construct(Manager $dcaManager, WidgetTypeBuilder $typeBuilder)
    {
        $this->dcaManager  = $dcaManager;
        $this->typeBuilder = $typeBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['dataContainer'])
            ->setDefaults(
                [
                    'fields'   => null,
                    'callback' => null,
                ]
            )
            ->setAllowedTypes('fields', 'array');
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['dataContainer'] instanceof Definition) {
            $definition = $options['dataContainer'];
        } else {
            $definition = $this->dcaManager->getDefinition($options['dataContainer']);
        }

        $fields = $this->getFieldConfigs($definition, $options);
        $next   = $this->createNextCallback($fields);

        while (($formField = $next())) {
            $this->typeBuilder->build($formField[0], $formField[1], $definition, $next, $builder);
        }
    }

    /**
     * @param Definition $definition
     * @param array      $options
     *
     * @return array
     */
    private function getFieldConfigs(Definition $definition, array $options): array
    {
        if ($options['fields']) {
            $fields = [];

            foreach ($options['fields'] as $name) {
                $fields[$name] = $definition->get(['fields', $name]);
            }

            return $fields;
        }

        $fields = $definition->get('fields') ?: [];
        if (!$options['callback']) {
            return $fields;
        }

        $filtered = [];
        foreach ($fields as $name => $config) {
            if ($config = $options['callback']($config, $name)) {
                $filtered[$name] = $config;
            }
        }

        return $filtered;
    }

    /**
     * Crate the next callback.
     *
     * @param array[] $formFields Form fields array.
     *
     * @return callable
     */
    private function createNextCallback(&$formFields): callable
    {
        return function (?callable $condition = null) use (&$formFields) {
            $current = current($formFields);

            if ($current === false) {
                return null;
            }

            if (!$condition || $condition($current)) {
                $key = key($formFields);
                next($formFields);

                return [$key, $current];
            }

            return null;
        };
    }
}
