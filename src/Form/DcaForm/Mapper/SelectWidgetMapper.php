<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

/**
 * Class SelectWidgetMapper maps the select widget to the ChoicesType
 */
final class SelectWidgetMapper extends AbstractChoicesWidgetMapper
{
    /**
     * The type class.
     */
    protected string $widgetType = 'select';

    /**
     * Display the choices expanded.
     */
    protected bool $expanded = false;
}
