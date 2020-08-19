<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

final class SelectWidgetMapper extends AbstractChoicesWidgetMapper
{
    protected $widgetType = 'select';

    /**
     * Display the choices expanded.
     *
     * @var bool
     */
    protected $expanded = false;
}
