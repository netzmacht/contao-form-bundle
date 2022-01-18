<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

use Netzmacht\ContaoFormBundle\Form\DcaForm\Context;
use Netzmacht\ContaoFormBundle\Form\DcaForm\WidgetTypeBuilder;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CheckboxWidgetMapper maps the checkbox widget to the CheckboxType
 */
final class CheckboxWidgetMapper extends AbstractWidgetMapper
{
    /**
     * The type class.
     *
     * @var string
     */
    protected $widgetType = 'checkbox';

    /**
     * The field type.
     *
     * @var string
     */
    protected $typeClass = CheckboxType::class;

    /**
     * {@inheritDoc}
     */
    public function supports(string $name, array $config): bool
    {
        if (! parent::supports($name, $config)) {
            return false;
        }

        return empty($config['eval']['multiple']);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(
        string $name,
        array $config,
        Context $context,
        WidgetTypeBuilder $fieldTypeBuilder,
        callable $next
    ): array {
        $options = parent::getOptions($name, $config, $context, $fieldTypeBuilder, $next);

        $options['false_values'] = ['', false, 0, null];

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function configure(FormBuilderInterface $formType, array $config, Context $context): void
    {
        parent::configure($formType, $config, $context);

        $formType->addModelTransformer(
            new CallbackTransformer(
                /** @param mixed $value */
                static function ($value): bool {
                    return (bool) $value;
                },
                /** @param mixed $value */
                static function ($value): string {
                    return $value ? '1' : '';
                }
            )
        );
    }
}
