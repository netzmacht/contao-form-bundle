<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

final class CheckboxFieldMapper extends AbstractChoicesFieldMapper
{
    /**
     * The form field type.
     */
    protected string $fieldType = 'checkbox';

    /**
     * Multiple. If null, the value is read from the model.
     */
    protected ?bool $multiple = true;
}
