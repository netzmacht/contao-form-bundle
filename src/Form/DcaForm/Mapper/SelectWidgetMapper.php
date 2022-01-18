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
     *
     * @var string
     */
    protected $widgetType = 'select';

    /**
     * Display the choices expanded.
     *
     * @var bool
     */
    protected $expanded = false;
}
