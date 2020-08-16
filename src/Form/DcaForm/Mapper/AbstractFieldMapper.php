<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

use Assert\Assert;
use Assert\AssertionFailedException;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\ContaoFormBundle\Form\DcaForm\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\DcaForm\FormFieldMapper;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Required;

/**
 * Class AbstractFieldMapper
 */
abstract class AbstractFieldMapper implements FormFieldMapper
{
    /**
     * The type class.
     *
     * @var string
     */
    protected $typeClass;

    /**
     * The field type.
     *
     * @var string
     */
    protected $widgetType;

    /**
     * Attributes which should be handled.
     *
     * @var array
     */
    protected $attributes = [
        'accesskey'   => true,
        'tabindex'    => true,
        'class'       => true,
        'placeholder' => true,
    ];

    /**
     * Optional mapping.
     *
     * @var bool[]
     */
    protected $options = [
        'label'     => true,
        'mandatory' => true,
        'minlength' => true,
        'maxlength' => true,
        'value'     => true,
    ];

    /**
     * Constructor.
     *
     * @throws AssertionFailedException When type class or field type is not givvn.
     */
    public function __construct()
    {
        Assert::that($this->typeClass)->string()->notBlank();
        Assert::that($this->widgetType)->string()->notBlank();
    }

    /**
     * {@inheritDoc}
     */
    public function supports(string $name, array $config) : bool
    {
        return $this->widgetType === $name;
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
     */
    public function getOptions(
        string $name,
        array $config,
        Definition $definition,
        FieldTypeBuilder $fieldTypeBuilder,
        callable $next
    ): array {
        $options = [
            'attr'  => $this->getAttributes($config),
            'help'  => $config['label'][1] ?? null,
            'widget' => [
                'class'    => $config['eval']['class'] ?? null,
                'be_class' => $config['eval']['tl_class'] ?? null,
                'fe_class' => $config['eval']['fe_class'] ?? null,
            ],
        ];

        if ($this->options['label']) {
            $options['label'] = $config['label'][0] ?? $name;
        }

        $required = (bool) ($config['mandatory'] ?? false);
        if ($this->options['mandatory'] && $required) {
            $options['required']      = $required;
            $options['constraints'][] = new Required();
        }

        if ($this->options['minlength'] && $model->maxlength > 0) {
            $options['attr']['minlength'] = $model->maxlength;
            $options['constraints'][]     = new Length(['min' => (int) $model->minlength]);
        }

        if ($this->options['maxlength'] && $model->maxlength > 0) {
            $options['attr']['maxlength'] = $model->maxlength;
            $options['constraints'][]     = new Length(['max' => (int) $model->maxlength]);
        }

        if ($this->options['value'] && $model->value) {
            $options['data'] = $model->value;
        }

        return $options;
    }

    protected function getAttributes(array $config): array
    {
        $attributes = [];
    }
}