<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

class RadioFieldMapper extends AbstractChoicesFieldMapper
{
    /**
     * The form field type.
     */
    protected string $fieldType = 'radio';

    /**
     * Multiple. If null, the value is read from the model.
     */
    protected ?bool $multiple = false;
}
