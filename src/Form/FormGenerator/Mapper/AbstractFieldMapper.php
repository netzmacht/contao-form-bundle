<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Assert\Assertion;
use Contao\FormFieldModel;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FormFieldMapper;
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
    protected $fieldType;

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
        'value'       => 'data'
    ];

    /**
     * Optional mapping.
     *
     * @var array
     */
    protected $options = [
        'label'     => true,
        'mandatory' => true,
        'minlength' => true,
        'maxlength' => true
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        Assertion::string($this->typeClass);
        Assertion::string($this->fieldType);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(FormFieldModel $model): bool
    {
        return $model->type === $this->fieldType;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeClass(FormFieldModel $model): string
    {
        return $this->typeClass;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $typeBuilder, callable $next): array
    {
        $options = [
            'attr' => $this->getAttributes($model),
        ];

        if ($this->options['label']) {
            $options['label'] = $model->label;
        }
        
        if ($this->options['mandatory']) {
            $options['required']      = (bool) $model->mandatory;
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

        return $options;
    }

    /**
     * Get the attributes.
     *
     * @param FormFieldModel $formFieldModel The form field model.
     *
     * @return array
     */
    private function getAttributes(FormFieldModel $formFieldModel): array
    {
        $attributes = [];

        foreach ($this->attributes as $property => $attribute) {
            if ($attribute === false) {
                continue;
            }

            if ($attribute === true) {
                $property = $attribute;
            }

            if ($formFieldModel->$property) {
                $attributes[$attribute] = $formFieldModel->$property;
            }
        }

        return $attributes;
    }
}
