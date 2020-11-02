<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\ContaoFormBundle\Form\DcaForm\WidgetTypeBuilder;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class CheckboxWidgetMapper extends AbstractWidgetMapper
{
    protected $widgetType = 'checkbox';

    protected $typeClass = CheckboxType::class;

    public function supports(string $name, array $config): bool
    {
        if (! parent::supports($name, $config)) {
            return false;
        }

        return empty($config['options']);
    }

    public function getOptions(
        string $name,
        array $config,
        Definition $definition,
        WidgetTypeBuilder $fieldTypeBuilder,
        callable $next
    ): array {
        $options = parent::getOptions(
            $name,
            $config,
            $definition,
            $fieldTypeBuilder,
            $next
        );

        $options['false_values'] = ['', false, 0, null];

        return $options;
    }

    public function configure(FormBuilderInterface $formType, array $config, Definition $definition): void
    {
        parent::configure($formType, $config, $definition);

        $formType->addModelTransformer(
            new CallbackTransformer(
                static function ($value) {
                    return (bool) $value;
                },
                static function ($value) {
                    return $value ? '1' : '';
                }
            )
        );
    }
}