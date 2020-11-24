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

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class TextareaWidgetMapper maps the textarea widget to the TextareaType
 */
final class TextareaWidgetMapper extends AbstractWidgetMapper
{
    /**
     * The type class.
     *
     * @var string
     */
    protected $widgetType = 'textarea';

    /**
     * The type class.
     *
     * @var string
     */
    protected $typeClass = TextareaType::class;

    /**
     * {@inheritDoc}
     */
    public function __construct(ContaoFrameworkInterface $framework)
    {
        parent::__construct($framework);

        $this->attributes['rows'] = true;
        $this->attributes['cols'] = true;
    }
}
