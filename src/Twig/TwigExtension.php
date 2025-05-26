<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Twig;

use Netzmacht\Contao\Toolkit\Security\Csrf\CsrfTokenProvider;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TwigExtension extends AbstractExtension
{
    /** @param CsrfTokenProvider $csrfTokenProvider Csrf token manager. */
    public function __construct(private readonly CsrfTokenProvider $csrfTokenProvider)
    {
    }

    /** @return list<TwigFunction> */
    #[Override]
    public function getFunctions(): array
    {
        return [new TwigFunction('contao_request_token', [$this, 'requestToken'])];
    }

    public function requestToken(): string
    {
        return $this->csrfTokenProvider->getTokenValue();
    }
}
