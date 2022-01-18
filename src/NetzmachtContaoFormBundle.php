<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\AddTaggedServicesAsArgumentPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetzmachtContaoFormBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentPass(
                'netzmacht.contao_form.form_generator.type_builder',
                'netzmacht.contao_form.form_generator.mapper'
            )
        );

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentPass(
                'netzmacht.contao_form.dca_form.type_builder',
                'netzmacht.contao_form.dca_form.mapper'
            )
        );
    }
}
