<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Twig;

use Netzmacht\Contao\Toolkit\Security\Csrf\CsrfTokenProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TwigExtension extends AbstractExtension
{
    /** @var CsrfTokenProvider */
    private $csrfTokenProvider;

    /**
     * @param CsrfTokenProvider $csrfTokenProvider Csrf token manager.
     */
    public function __construct(CsrfTokenProvider $csrfTokenProvider)
    {
        $this->csrfTokenProvider = $csrfTokenProvider;
    }

    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [new TwigFunction('contao_request_token', [$this, 'requestToken'])];
    }

    public function requestToken(): string
    {
        return $this->csrfTokenProvider->getTokenValue();
    }
}
