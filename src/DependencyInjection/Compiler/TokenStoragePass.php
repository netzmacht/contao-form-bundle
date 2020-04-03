<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TokenStoragePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) : void
    {
        $tokenStorageId = $container->has('contao.csrf.token_storage')
            ? 'contao.csrf.token_storage'
            : 'security.csrf.token_storage';

        $container->setAlias('netzmacht.contao_form.csrf.token_storage', $tokenStorageId);
    }
}
