<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\DependencyInjection;

use Override;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use function dirname;

final class NetzmachtContaoFormExtension extends Extension
{
    /** {@inheritDoc} */
    #[Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(dirname(__DIR__) . '/Resources/config'),
        );

        $loader->load('services.yml');
        $loader->load('form_generator.yml');
        $loader->load('dca_form.yml');
    }
}
