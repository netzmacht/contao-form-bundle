<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Validator\Constraints;

use Contao\Widget;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

use function is_array;
use function sprintf;

/**
 * Class Rgxp is a symfony validator constraint for the Contao rgxp setting
 */
final class Rgxp extends Constraint
{
    /** The rgxp. */
    protected string $rgxp;

    /** The widget. */
    protected Widget|null $widget = null;

    /** The label.*/
    protected string|null $label = null;

    /**
     * @param string|array<string,mixed>|null $rgxp    The rgxp. Passing an array of options is supported for backward
     *                                                 compatibility.
     * @param Widget|null                     $widget  The widget.
     * @param string|null                     $label   The label.
     * @param list<string>|null               $groups  The validation groups.
     * @param mixed                           $payload Domain-specific data attached to a constraint.
     *
     * @throws MissingOptionsException When no rgxp is given.
     */
    #[HasNamedArguments]
    public function __construct(
        string|array|null $rgxp = null,
        Widget|null $widget = null,
        string|null $label = null,
        array|null $groups = null,
        mixed $payload = null,
    ) {
        if (is_array($rgxp)) {
            /** @psalm-var Widget|null $widget */
            $widget = $rgxp['widget'] ?? $widget;
            /** @psalm-var string|null $label */
            $label = $rgxp['label'] ?? $label;
            /** @psalm-var list<string>|null $groups */
            $groups  = $rgxp['groups'] ?? $groups;
            $payload = $rgxp['payload'] ?? $payload;
            /** @psalm-var string|null $rgxp */
            $rgxp = $rgxp['rgxp'] ?? null;
        }

        if ($rgxp === null) {
            throw new MissingOptionsException(
                sprintf('The option "rgxp" must be given for constraint "%s".', self::class),
                ['rgxp'],
            );
        }

        parent::__construct(null, $groups, $payload);

        $this->groups = [];
        $this->rgxp   = $rgxp;
        $this->widget = $widget;
        $this->label  = $label ?? $widget?->label;
    }

    public function getRgxp(): string
    {
        return $this->rgxp;
    }

    /** Get the label if defined. */
    public function getLabel(): string|null
    {
        return $this->label;
    }

    /** Get the widget is defined. */
    public function getWidget(): Widget|null
    {
        return $this->widget;
    }
}
