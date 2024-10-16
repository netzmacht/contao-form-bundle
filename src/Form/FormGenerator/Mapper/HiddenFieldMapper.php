<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator\Mapper;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

final class HiddenFieldMapper extends AbstractFieldMapper
{
    /**
     * The form field type.
     */
    protected string $fieldType = 'hidden';

    /**
     * The type class.
     */
    protected string $typeClass = HiddenType::class;

    /** {@inheritDoc} */
    public function __construct()
    {
        parent::__construct();

        $this->options['label']     = false;
        $this->options['maxlength'] = false;
        $this->options['minlength'] = false;
    }
}
