<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

use Assert\AssertionFailedException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Netzmacht\Contao\Toolkit\Callback\Invoker as CallbackInvoker;
use Netzmacht\ContaoFormBundle\Form\DcaForm\Context;
use Netzmacht\ContaoFormBundle\Form\DcaForm\WidgetTypeBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use function array_flip;
use function is_array;

/**
 * Class AbstractChoicesWidgetMapper is a base class for widgets being mapped to the symfony form ChoiceType
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class AbstractChoicesWidgetMapper extends AbstractWidgetMapper
{
    /**
     * The type class.
     *
     * @var string
     */
    protected $typeClass = ChoiceType::class;

    /**
     * Multiple. If null, the value is read from the model.
     *
     * @var bool|null
     */
    protected $multiple;

    /**
     * Display the choices expanded.
     *
     * @var bool
     */
    protected $expanded = true;

    /**
     * Callback invoker.
     *
     * @var CallbackInvoker
     */
    private $callbackInvoker;

    /**
     * @param ContaoFramework $framework       Contao framework.
     * @param CallbackInvoker $callbackInvoker Callback invoker.
     *
     * @throws AssertionFailedException When type class or field type is not given.
     */
    public function __construct(
        ContaoFramework $framework,
        CallbackInvoker $callbackInvoker
    ) {
        parent::__construct($framework);

        $this->options['maxlength'] = false;
        $this->options['minlength'] = false;
        $this->options['rgxp']      = false;

        $this->callbackInvoker = $callbackInvoker;
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

        $options['multiple']    = $this->multiple ?? (bool) ($config['eval']['multiple'] ?? false);
        $options['expanded']    = $this->expanded;
        $options['placeholder'] = false;

        if (! empty($config['eval']['includeBlankOption'])) {
            $options['placeholder'] = ($config['eval']['blankOptionLabel'] ?? '-');
        }

        $options = $this->parseOptionsConfig($name, $options, $config, $context);

        return $options;
    }

    /**
     * Build the choices.
     *
     * @param string              $name    Field name.
     * @param array<string,mixed> $options Form type options.
     * @param array<string,mixed> $config  Widget configuration.
     * @param Context             $context Data container context.
     *
     * @return array<string,mixed>
     */
    protected function parseOptionsConfig(string $name, array $options, array $config, Context $context): array
    {
        $options['choices'] = [];

        if (! empty($config['options_callback'])) {
            $driver             = $context->getDriver();
            $arguments          = $driver ? [$driver] : [];
            $choices            = $this->callbackInvoker->invoke($config['options_callback'], $arguments);
            $options['choices'] = array_flip($choices);

            return $options;
        }

        if (empty($config['options']) || ! is_array($config['options'])) {
            return $options;
        }

        $formatter = $context->getFormatter();

        foreach ($config['options'] as $option) {
            $label                      = $formatter->formatValue($name, $option, $context->getDriver()) ?: $option;
            $options['choices'][$label] = $option;
        }

        return $options;
    }
}
