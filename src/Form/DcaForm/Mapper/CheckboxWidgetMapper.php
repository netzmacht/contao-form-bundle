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

use Netzmacht\Contao\Toolkit\Dca\Definition;
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

        return empty($config['options']);
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
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
