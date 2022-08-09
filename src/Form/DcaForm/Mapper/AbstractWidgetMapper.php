<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

use Assert\Assert;
use Assert\AssertionFailedException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\StringUtil;
use Contao\Widget;
use Netzmacht\ContaoFormBundle\Form\DcaForm\Context;
use Netzmacht\ContaoFormBundle\Form\DcaForm\WidgetMapper;
use Netzmacht\ContaoFormBundle\Form\DcaForm\WidgetTypeBuilder;
use Netzmacht\ContaoFormBundle\Validator\Constraints\Rgxp;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Required;

abstract class AbstractWidgetMapper implements WidgetMapper
{
    /**
     * The type class.
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected string $typeClass;

    /**
     * The field type.
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected string $widgetType;

    /**
     * Attributes which should be handled.
     *
     * @var array<string,bool|string>
     */
    protected array $attributes = [
        'accesskey'   => true,
        'tabindex'    => true,
        'class'       => true,
        'placeholder' => true,
    ];

    /**
     * Optional mapping.
     *
     * @var array<string,bool>
     */
    protected array $options = [
        'label'     => true,
        'mandatory' => true,
        'minlength' => true,
        'maxlength' => true,
        'emptyData' => true,
        'rgxp'      => true,
    ];

    /**
     * Contao framework.
     */
    private ContaoFramework $framework;

    /**
     * @param ContaoFramework $framework Contao framework.
     *
     * @throws AssertionFailedException When type class or field type is not given.
     */
    public function __construct(ContaoFramework $framework)
    {
        /** @psalm-suppress UninitializedProperty */
        Assert::that($this->typeClass)->string()->notBlank();
        /** @psalm-suppress UninitializedProperty */
        Assert::that($this->widgetType)->string()->notBlank();

        $this->framework = $framework;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(string $name, array $config): bool
    {
        if (! isset($config['inputType'])) {
            return false;
        }

        return $this->widgetType === $config['inputType'];
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeClass(string $name, array $config): string
    {
        return $this->typeClass;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getOptions(
        string $name,
        array $config,
        Context $context,
        WidgetTypeBuilder $fieldTypeBuilder,
        callable $next
    ): array {
        $options = [
            'attr'   => $this->getAttributes($config),
            'help'   => ($config['label'][1] ?? null),
            'contaoWidget' => [
                'class'    => ($config['eval']['class'] ?? null),
                'be_class' => ($config['eval']['tl_class'] ?? null),
                'fe_class' => ($config['eval']['fe_class'] ?? null),
            ],
        ];

        if ($this->options['label']) {
            $options['label'] = ($config['label'][0] ?? $name);
        }

        $required            = (bool) ($config['eval']['mandatory'] ?? false);
        $options['required'] = $required;
        if ($this->options['mandatory'] && $required) {
            $options['constraints'][] = new Required();
        }

        if ($this->options['minlength'] && isset($config['eval']['minlength'])) {
            $options['attr']['minlength'] = $config['eval']['minlength'];
            $options['constraints'][]     = new Length(['min' => (int) $config['eval']['minlength']]);
        }

        if ($this->options['maxlength'] && isset($config['eval']['maxlength'])) {
            $options['attr']['maxlength'] = $config['eval']['maxlength'];
            $options['constraints'][]     = new Length(['max' => (int) $config['eval']['maxlength']]);
        }

        if ($this->options['emptyData']) {
            $options['empty_data'] = empty($config['eval']['nullIfEmpty']) ? '' : null;
        }

        if ($this->options['rgxp'] && isset($config['eval']['rgxp'])) {
            $options['constraints'][] = new Rgxp(
                [
                    'rgxp'   => $config['eval']['rgxp'],
                    'label'  => StringUtil::decodeEntities(($config['label'][0] ?? $name)),
                    'widget' => $this->createWidget($name, $config, $context),
                ]
            );
        }

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function configure(FormBuilderInterface $formType, array $config, Context $context): void
    {
        $formType->addModelTransformer(
            new CallbackTransformer(
                /**
                 * @param mixed $value
                 *
                 * @return mixed
                 */
                static function ($value) {
                    return $value;
                },
                /**
                 * @param mixed $value
                 *
                 * @return mixed
                 */
                function ($value) use ($config) {
                    if ($value === null) {
                        $this->framework->initialize();

                        return $this->framework
                            ->getAdapter(Widget::class)
                            ->__call('getEmptyValueByFieldType', [$config['sql']]);
                    }

                    return $value;
                }
            )
        );
    }

    /**
     * Get the attributes based on the attributes configuration of the mapper.
     *
     * @param array<string,mixed> $config The widget configuration.
     *
     * @return array<string,mixed>
     */
    protected function getAttributes(array $config): array
    {
        $attributes = [];

        foreach ($this->attributes as $property => $attribute) {
            if ($attribute === false) {
                continue;
            }

            if ($attribute === true) {
                $attribute = $property;
            }

            if (! isset($config['eval'][$property])) {
                continue;
            }

            $attributes[$attribute] = $config['eval'][$property];
        }

        return $attributes;
    }

    /**
     * Create the contao widget.
     *
     * @param string              $name    The field name.
     * @param array<string,mixed> $config  The configuration.
     * @param Context             $context The Data container context.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function createWidget(string $name, array $config, Context $context): ?Widget
    {
        $this->framework->initialize();

        if (! isset($GLOBALS['BE_FFL'][$config['inputType']])) {
            return null;
        }

        /** @var class-string<Widget> $widgetClass */
        $widgetClass = $GLOBALS['BE_FFL'][$config['inputType']];
        $attributes  = $widgetClass::getAttributesFromDca(
            $config,
            $name,
            null,
            $name,
            $context->getDefinition()->getName()
        );

        /** @psalm-suppress UnsafeInstantiation */
        return new $widgetClass($attributes);
    }
}
