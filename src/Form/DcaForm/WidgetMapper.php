<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm;

use Symfony\Component\Form\FormBuilderInterface;

interface WidgetMapper
{
    /**
     * Check if type mapper supports the form field.
     *
     * @param string              $name   The form field name.
     * @param array<string,mixed> $config The form field config.
     */
    public function supports(string $name, array $config): bool;

    /**
     * Get the type class from the form field.
     *
     * @param string              $name   The form field name.
     * @param array<string,mixed> $config The form field config.
     */
    public function getTypeClass(string $name, array $config): string;

    /**
     * Get the options for the form field.
     *
     * @param string              $name             The form field name.
     * @param array<string,mixed> $config           The form field config.
     * @param Context             $context          The data container context.
     * @param WidgetTypeBuilder   $fieldTypeBuilder The field type builder.
     * @param callable            $next             Callable to fetch the next element.
     *
     * @return array<string,mixed>
     */
    public function getOptions(
        string $name,
        array $config,
        Context $context,
        WidgetTypeBuilder $fieldTypeBuilder,
        callable $next
    ): array;

    /**
     * Configure the created form type
     *
     * @param FormBuilderInterface $formType The created form type builder.
     * @param array<string,mixed>  $config   The form field config.
     * @param Context              $context  The data container context.
     */
    public function configure(FormBuilderInterface $formType, array $config, Context $context): void;
}
