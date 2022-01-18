<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\FormGenerator;

use Contao\FormFieldModel;

interface FormFieldMapper
{
    /**
     * Check if type mapper supports the form field.
     *
     * @param FormFieldModel $model The form field.
     */
    public function supports(FormFieldModel $model): bool;

    /**
     * Get the name of the form field.
     *
     * @param FormFieldModel $model The form field.
     */
    public function getName(FormFieldModel $model): ?string;

    /**
     * Get the type class from the form field.
     *
     * @param FormFieldModel $model The form field.
     */
    public function getTypeClass(FormFieldModel $model): string;

    /**
     * Get the options for the form field.
     *
     * @param FormFieldModel   $model            The form field.
     * @param FieldTypeBuilder $fieldTypeBuilder The field type builder.
     * @param callable         $next             Callable to fetch the next element.
     *
     * @return array<string,mixed>
     */
    public function getOptions(
        FormFieldModel $model,
        FieldTypeBuilder $fieldTypeBuilder,
        callable $next
    ): array;
}
