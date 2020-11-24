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

namespace Netzmacht\ContaoFormBundle\Form;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\ContaoFormBundle\Form\DcaForm\Context;
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
     * The widget type builder.
     *
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
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['dataContainer'])
            ->setDefaults(
                [
                    'formatter' => null,
                    'driver'    => null,
                    'fields'    => null,
                    'callback'  => null,
                ]
            )
            ->setAllowedTypes('fields', 'array');
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['dataContainer'] instanceof Definition) {
            $definition = $options['dataContainer'];
        } else {
            $definition = $this->dcaManager->getDefinition($options['dataContainer']);
        }

        $context = new Context(
            $definition,
            $options['formatter'] ?: $this->dcaManager->getFormatter($definition->getName()),
            $options['driver']
        );

        $fields = $this->getFieldConfigs($definition, $options);
        $next   = $this->createNextCallback($fields);

        while (($formField = $next())) {
            $this->typeBuilder->build($formField[0], $formField[1], $context, $next, $builder);
        }
    }

    /**
     * Get the fields configurations.
     *
     * @param Definition $definition The given definition.
     * @param array      $options    The given options.
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
        return static function (?callable $condition = null) use (&$formFields) {
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
