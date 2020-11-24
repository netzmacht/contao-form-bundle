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

use Assert\AssertionFailedException;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\ContaoFormBundle\Form\DcaForm\WidgetTypeBuilder;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class PasswordWidgetMapper maps the password widget to the RepeatedType using the PasswordType
 */
final class PasswordWidgetMapper extends AbstractWidgetMapper
{
    /**
     * The widget type.
     *
     * @var string
     */
    protected $widgetType = 'password';

    /**
     * The type class.
     *
     * @var string
     */
    protected $typeClass = RepeatedType::class;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param ContaoFrameworkInterface $framework  Contao framework.
     * @param Translator               $translator The translator.
     *
     * @throws AssertionFailedException When type class or field type is not given.
     */
    public function __construct(ContaoFrameworkInterface $framework, Translator $translator)
    {
        parent::__construct($framework);

        $this->translator = $translator;

        $this->options['label'] = false;
        $this->options['value'] = false;
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

        $options['type']           = PasswordType::class;
        $options['first_options']  = [
            'label' => ($config['label'][0] ?? $name),
        ];
        $options['second_options'] = [
            'label' => $this->translator->trans(
                'MSC.confirmation',
                [$options['first_options']['label']],
                'contao_default'
            ),
        ];

        return $options;
    }
}
