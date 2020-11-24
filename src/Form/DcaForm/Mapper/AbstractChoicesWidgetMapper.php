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

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Netzmacht\Contao\Toolkit\Callback\Invoker as CallbackInvoker;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\ContaoFormBundle\Form\DcaForm\WidgetTypeBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use function array_flip;
use function is_array;

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
     * Data container manager.
     *
     * @var DcaManager
     */
    private $dcaManager;

    /**
     * Callback invoker.
     *
     * @var CallbackInvoker
     */
    private $callbackInvoker;

    /**
     * Constructor.
     *
     * @param ContaoFrameworkInterface $framework       Contao framework.
     * @param DcaManager               $dcaManager      Data container manager.
     * @param CallbackInvoker          $callbackInvoker Callback invoker.
     *
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(ContaoFrameworkInterface $framework, DcaManager $dcaManager, CallbackInvoker $callbackInvoker)
    {
        parent::__construct($framework);

        $this->options['maxlength'] = false;
        $this->options['minlength'] = false;
        $this->options['rgxp']      = false;

        $this->dcaManager      = $dcaManager;
        $this->callbackInvoker = $callbackInvoker;
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

        $options['multiple']    = $this->multiple === null ? (bool) ($config['eval']['multiple'] ?? false) : $this->multiple;
        $options['expanded']    = $this->expanded;
        $options['placeholder'] = false;

        if (!empty($config['eval']['includeBlankOption'])) {
            $options['placeholder'] = $config['eval']['blankOptionLabel'] ?? '-';
        }

        $options = $this->parseOptionsConfig($name, $options, $config, $definition);

        return $options;
    }

    /**
     * Build the choices.
     *
     * @param string     $name       Field name.
     * @param array      $options    Form type options.
     * @param array      $config     Widget configuration.
     * @param Definition $definition Data container definition.
     *
     * @return array
     */
    protected function parseOptionsConfig(string $name, array $options, array $config, Definition $definition): array
    {
        $options['choices'] = [];

        if (!empty($config['options_callback'])) {
            // Fixme: Pass instance of DataContainer
            $choices            = $this->callbackInvoker->invoke($config['options_callback']);
            $options['choices'] = array_flip($choices);

            return $options;
        }

        if (empty($config['options']) || !is_array($config['options'])) {
            return $options;
        }

        $formatter = $this->dcaManager->getFormatter($definition->getName());

        foreach ($config['options'] as $option) {
            // Fixme: Pass data container for options callbacks
            $label                      = $formatter->formatValue($name, $option) ?: $option;
            $options['choices'][$label] = $option;
        }

        return $options;
    }
}
