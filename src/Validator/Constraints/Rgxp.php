<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Validator\Constraints;

use Contao\Widget;
use Symfony\Component\Validator\Constraint;

use function array_merge;
use function is_array;

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
     *
     * @psalm-param mixed $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->groups = [];
        $this->rgxp   = is_array($options) ? ($options['rgxp'] ?? '') : '';

        if ($this->label || ! ($this->widget instanceof Widget)) {
            return;
        }

        $this->label = $this->widget->label;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions(): array
    {
        return array_merge(['rgxp'], parent::getRequiredOptions());
    }

    /**
     * Get the rgxp.
     */
    public function getRgxp(): string
    {
        return $this->rgxp;
    }

    /**
     * Get the label if defined.
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Get the widget is defined.
     */
    public function getWidget(): ?Widget
    {
        return $this->widget;
    }
}
