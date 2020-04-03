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

namespace Netzmacht\ContaoFormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class TokenStoragePass registers the csrf token storage alias
 *
 * It creates an alias netzmacht.contao_form.csrf.token_storage to the csrf token storage used by Contao (it differs
 * depending on the Contao version).
 */
final class TokenStoragePass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container) : void
    {
        $tokenStorageId = $container->has('contao.csrf.token_storage')
            ? 'contao.csrf.token_storage'
            : 'security.csrf.token_storage';

        $container->setAlias('netzmacht.contao_form.csrf.token_storage', $tokenStorageId);
    }
}
