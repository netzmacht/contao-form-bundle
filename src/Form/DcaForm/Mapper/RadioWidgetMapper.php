<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2020 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */

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
