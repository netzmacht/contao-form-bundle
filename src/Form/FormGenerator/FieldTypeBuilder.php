<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator;

use Contao\FormFieldModel;

class FieldTypeBuilder
{
    /**
     * Form field mappers.
     *
     * @var FormFieldMapper[]
     */
    private $mappers;

    /**
     * @param FormFieldMapper[] $mappers Form field mappers.
     */
    public function __construct(array $mappers)
    {
        $this->mappers = $mappers;
    }

    /**
     * Build the form field type.
     *
     * @param FormFieldModel $fieldModel The field model.
     * @param callable       $next       Callback to get the next form field model.
     *
     * @return array<string,mixed>|null
     */
    public function build(FormFieldModel $fieldModel, $next): ?array
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->supports($fieldModel)) {
                return [
                    'name'    => $mapper->getName($fieldModel),
                    'type'    => $mapper->getTypeClass($fieldModel),
                    'options' => $mapper->getOptions($fieldModel, $this, $next),
                ];
            }
        }

        return null;
    }
}
