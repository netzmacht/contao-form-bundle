<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Assert\Assert;
use Assert\AssertionFailedException;
use Contao\FormFieldModel;
use Contao\StringUtil;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FieldTypeBuilder;
use Netzmacht\ContaoFormBundle\Form\FormGenerator\FormFieldMapper;
use Netzmacht\ContaoFormBundle\Validator\Constraints\Rgxp;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Required;

abstract class AbstractFieldMapper implements FormFieldMapper
{
    /**
     * The type class.
     *
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $typeClass;

    /**
     * The field type.
     *
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $fieldType;

    /**
     * Attributes which should be handled.
     *
     * @var array<string,bool|string>
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
     * @var array<string,bool|string>
     */
    protected $options = [
        'label'     => true,
        'mandatory' => true,
        'minlength' => true,
        'maxlength' => true,
        'value'     => true,
        'rgxp'      => true,
    ];

    /**
     * @throws AssertionFailedException When type class or field type is not givvn.
     */
    public function __construct()
    {
        /** @psalm-suppress UninitializedProperty */
        Assert::that($this->typeClass)->string()->notBlank();
        /** @psalm-suppress UninitializedProperty */
        Assert::that($this->fieldType)->string()->notBlank();
    }

    public function supports(FormFieldModel $model): bool
    {
        return $model->type === $this->fieldType;
    }

    public function getName(FormFieldModel $model): ?string
    {
        return $model->name;
    }

    public function getTypeClass(FormFieldModel $model): string
    {
        return $this->typeClass;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getOptions(FormFieldModel $model, FieldTypeBuilder $fieldTypeBuilder, callable $next): array
    {
        $options = [
            'attr' => $this->getAttributes($model),
        ];

        if ($this->options['label']) {
            $options['label'] = StringUtil::decodeEntities($model->label);
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

        if ($this->options['value'] && $model->value) {
            $options['data'] = $model->value;
        }

        if ($this->options['rgxp'] && $model->rgxp) {
            $options['constraints'][] = new Rgxp(
                [
                    'rgxp'  => $model->rgxp,
                    'label' => StringUtil::decodeEntities($model->label),
                ]
            );
        }

        return $options;
    }

    /**
     * Get the attributes.
     *
     * @param FormFieldModel $formFieldModel The form field model.
     *
     * @return array<string,mixed>
     */
    private function getAttributes(FormFieldModel $formFieldModel): array
    {
        $attributes = [];

        foreach ($this->attributes as $property => $attribute) {
            if ($attribute === false) {
                continue;
            }

            if ($attribute === true) {
                $attribute = $property;
            }

            if (! $formFieldModel->$property) {
                continue;
            }

            $attributes[$attribute] = $formFieldModel->$property;
        }

        return $attributes;
    }
}
