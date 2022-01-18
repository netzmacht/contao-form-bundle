<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Form\DcaForm;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Formatter;

/**
 * The Context describes the data container context providing access to the definition, the formatter and optionally
 * the given driver.
 */
final class Context
{
    /**
     * The data container definition.
     *
     * @var Definition
     */
    private $definition;

    /**
     * The formatter.
     *
     * @var Formatter
     */
    private $formatter;

    /**
     * The data container driver.
     *
     * @var DataContainer|null
     */
    private $driver;

    /**
     * @param Definition         $definition The data container definition.
     * @param Formatter          $formatter  The formatter.
     * @param DataContainer|null $driver     The data container driver.
     */
    public function __construct(Definition $definition, Formatter $formatter, ?DataContainer $driver)
    {
        $this->definition = $definition;
        $this->formatter  = $formatter;
        $this->driver     = $driver;
    }

    /**
     * Get definition.
     */
    public function getDefinition(): Definition
    {
        return $this->definition;
    }

    /**
     * Get formatter.
     */
    public function getFormatter(): Formatter
    {
        return $this->formatter;
    }

    /**
     * Get driver.
     */
    public function getDriver(): ?DataContainer
    {
        return $this->driver;
    }
}
