<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

final class MultipleCheckboxesWizard extends AbstractChoicesWidgetMapper
{
    protected $widgetType = 'checkbox';

    public function supports(string $name, array $config): bool
    {
        if (parent::supports($name, $config)) {
            return !empty($config['eval']['multiple']);
        }

        if (!isset($config['inputType'])) {
            return false;
        }

        return $config['inputType'] === 'checkboxWizard';
    }
}
