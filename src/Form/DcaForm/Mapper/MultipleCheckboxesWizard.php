<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

/**
 * Class CheckboxWidgetMapper maps the checkbox and checkboxWizard widget to the CheckboxType
 */
final class MultipleCheckboxesWizard extends AbstractChoicesWidgetMapper
{
    /**
     * The widget type.
     */
    protected string $widgetType = 'checkbox';

    /**
     * Multiple options are allowed.
     */
    protected ?bool $multiple = true;

    /**
     * {@inheritDoc}
     */
    public function supports(string $name, array $config): bool
    {
        if (parent::supports($name, $config)) {
            return ! empty($config['eval']['multiple']);
        }

        if (! isset($config['inputType'])) {
            return false;
        }

        return $config['inputType'] === 'checkboxWizard';
    }
}
