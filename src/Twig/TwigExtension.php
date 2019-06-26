<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Twig;

use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TwigExtension extends AbstractExtension
{
    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var string */
    private $csrfTokenName;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, string $csrfTokenName)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->csrfTokenName    = $csrfTokenName;
    }

    public function getFunctions() : array
    {
        return [
            new TwigFunction('contao_request_token', [$this, 'requestToken'])
        ];
    }

    public function requestToken() : string
    {
        return $this->csrfTokenManager->getToken($this->csrfTokenName)->getValue();
    }
}
