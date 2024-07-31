<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\ContaoFormBundle\Form\DcaForm\Context;
use Netzmacht\ContaoFormBundle\Form\DcaForm\WidgetTypeBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function current;
use function key;
use function next;

final class DcaFormType extends AbstractType
{
    /**
     * @param Manager           $dcaManager  Data container manager.
     * @param WidgetTypeBuilder $typeBuilder Field type builder.
     */
    public function __construct(private readonly Manager $dcaManager, private readonly WidgetTypeBuilder $typeBuilder)
    {
    }

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
                ],
            )
            ->setAllowedTypes('fields', 'array');
    }

    /** {@inheritDoc} */
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
            $options['driver'],
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
     * @param Definition          $definition The given definition.
     * @param array<string,mixed> $options    The given options.
     *
     * @return array<string,mixed>
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
        if (! $options['callback']) {
            return $fields;
        }

        $filtered = [];
        foreach ($fields as $name => $config) {
            $config = $options['callback']($config, $name);
            if (! $config) {
                continue;
            }

            $filtered[$name] = $config;
        }

        return $filtered;
    }

    /**
     * Crate the next callback.
     *
     * @param array<string,array<string,mixed>> $formFields Form fields array.
     */
    private function createNextCallback(array &$formFields): callable
    {
        // phpcs:ignore SlevomatCodingStandard.TypeHints.NullTypeHintOnLastPosition.NullTypeHintNotOnLastPosition
        /** @psalm-param null|callable(): bool $condition */

        return static function (callable|null $condition = null) use (&$formFields) {
            $current = current($formFields);

            if ($current === false) {
                return null;
            }

            if ($condition === null || $condition($current)) {
                $key = key($formFields);
                next($formFields);

                return [$key, $current];
            }

            return null;
        };
    }
}
