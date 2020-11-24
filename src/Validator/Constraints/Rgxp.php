<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Validator\Constraints;

use Contao\Widget;
use Symfony\Component\Validator\Constraint;

final class Rgxp extends Constraint
{
    /**
     * The rgxp.
     *
     * @var string
     */
    protected $rgxp;

    /**
     * @var Widget|null
     */
    protected $widget;

    /**
     * The label.
     *
     * @var string|null
     */
    protected $label;

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (!$this->label && $this->widget instanceof Widget) {
            $this->label  = $this->widget->label;
        }
    }

    public function getRequiredOptions(): array
    {
        return ['rgpx'] + parent::getRequiredOptions();
    }

    public function getRgxp(): string
    {
        return $this->rgxp;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getWidget(): ?Widget
    {
        return $this->widget;
    }
}
