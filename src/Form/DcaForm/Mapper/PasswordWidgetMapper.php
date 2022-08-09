<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

use Assert\AssertionFailedException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Netzmacht\ContaoFormBundle\Form\DcaForm\Context;
use Netzmacht\ContaoFormBundle\Form\DcaForm\WidgetTypeBuilder;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

/**
 * Class PasswordWidgetMapper maps the password widget to the RepeatedType using the PasswordType
 */
final class PasswordWidgetMapper extends AbstractWidgetMapper
{
    /**
     * The widget type.
     */
    protected string $widgetType = 'password';

    /**
     * The type class.
     */
    protected string $typeClass = RepeatedType::class;

    /**
     * Translator.
     */
    private Translator $translator;

    /**
     * @param ContaoFramework $framework  Contao framework.
     * @param Translator      $translator The translator.
     *
     * @throws AssertionFailedException When type class or field type is not given.
     */
    public function __construct(ContaoFramework $framework, Translator $translator)
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
        Context $context,
        WidgetTypeBuilder $fieldTypeBuilder,
        callable $next
    ): array {
        $options = parent::getOptions($name, $config, $context, $fieldTypeBuilder, $next);

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
