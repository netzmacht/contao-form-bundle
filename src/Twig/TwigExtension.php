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

namespace Netzmacht\ContaoFormBundle\Twig;

use Netzmacht\Contao\Toolkit\Security\Csrf\CsrfTokenProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigExtension
 */
final class TwigExtension extends AbstractExtension
{
    /** @var CsrfTokenProvider */
    private $csrfTokenProvider;

    /**
     * TwigExtension constructor.
     *
     * @param CsrfTokenProvider $csrfTokenProvider Csrf token manager.
     */
    public function __construct(CsrfTokenProvider $csrfTokenProvider)
    {
        $this->csrfTokenProvider = $csrfTokenProvider;
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
        return $this->csrfTokenProvider->getTokenValue();
    }
}
