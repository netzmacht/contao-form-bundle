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

namespace Netzmacht\ContaoFormBundle\Validator\Constraints;

use Contao\Widget;
use Symfony\Component\Validator\Constraint;

/**
 * Class Rgxp is a symfony validator constraint for the Contao rgxp setting
 */
final class Rgxp extends Constraint
{
    /**
     * The rgxp.
     *
     * @var string
     */
    protected $rgxp;

    /**
     * The widget.
     *
     * @var Widget|null
     */
    protected $widget;

    /**
     * The label.
     *
     * @var string|null
     */
    protected $label;

    /**
     * {@inheritDoc}
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if (!$this->label && $this->widget instanceof Widget) {
            $this->label = $this->widget->label;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions(): array
    {
        return array_merge(['rgpx'], parent::getRequiredOptions());
    }

    /**
     * Get the rgxp.
     *
     * @return string
     */
    public function getRgxp(): string
    {
        return $this->rgxp;
    }

    /**
     * Get the label if defined.
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Get the widget is defined.
     *
     * @return string
     */
    public function getWidget(): ?Widget
    {
        return $this->widget;
    }
}
