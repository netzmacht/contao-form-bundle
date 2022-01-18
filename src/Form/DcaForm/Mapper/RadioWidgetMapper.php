<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm\Mapper;

/**
 * Class RadioWidgetMapper maps the radio widget to the ChoicesType
 */
final class RadioWidgetMapper extends AbstractChoicesWidgetMapper
{
    /**
     * The type class.
     *
     * @var string
     */
    protected $widgetType = 'radio';
}
