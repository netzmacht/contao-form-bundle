<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Tests\Fixtures;

use Contao\Widget;
use Override;

/**
 * Minimal widget which can be created without a booted Contao framework.
 *
 * @psalm-suppress PropertyNotSetInConstructor - The parent constructor is skipped on purpose
 */
final class TestWidget extends Widget
{
    public function __construct(string $label = '')
    {
        $this->strLabel = $label;
    }

    /**
     * Contao's implementation requires a booted container, so only the error is recorded.
     *
     * @param string $strError The error message.
     */
    #[Override]
    public function addError($strError): void
    {
        $this->arrErrors[] = $strError;
    }

    #[Override]
    public function generate(): string
    {
        return '';
    }
}
