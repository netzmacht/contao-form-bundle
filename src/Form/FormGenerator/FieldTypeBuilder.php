<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator;

use Contao\FormFieldModel;

final class FieldTypeBuilder
{
    /**
     * Form field mappers.
     *
     * @var FormFieldMapper[]
     */
    private array $mappers;

    /**
     * @param FormFieldMapper[] $mappers Form field mappers.
     */
    public function __construct(iterable $mappers)
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
