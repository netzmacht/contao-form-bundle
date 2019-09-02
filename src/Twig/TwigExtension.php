<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017-2019 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\Twig;

use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigExtension
 */
final class TwigExtension extends AbstractExtension
{
    /** @var TokenStorageInterface */
    private $csrfTokenStorage;

    /** @var string */
    private $csrfTokenName;

    /**
     * TwigExtension constructor.
     *
     * @param TokenStorageInterface $csrfTokenStorage Csrf token storage.
     * @param string                $csrfTokenName    Csrf token name.
     */
    public function __construct(TokenStorageInterface $csrfTokenStorage, string $csrfTokenName)
    {
        $this->csrfTokenStorage = $csrfTokenStorage;
        $this->csrfTokenName    = $csrfTokenName;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('contao_request_token', [$this, 'requestToken'])
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function requestToken() : string
    {
        return $this->csrfTokenStorage->getToken($this->csrfTokenName);
    }
}
