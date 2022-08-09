<?php

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\ManagerPlugin\Config\ExtensionPluginInterface;
use Netzmacht\Contao\Toolkit\Bundle\NetzmachtContaoToolkitBundle;
use Netzmacht\ContaoFormBundle\NetzmachtContaoFormBundle;

use function dirname;

final class Plugin implements BundlePluginInterface, ExtensionPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(NetzmachtContaoFormBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, NetzmachtContaoToolkitBundle::class]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionConfig($extensionName, array $extensionConfigs, ContainerBuilder $container): array
    {
        if ($extensionName === 'framework') {
            $extensionConfigs[] = ['form' => true];
        }

        if ($extensionName === 'twig') {
            $extensionConfigs[] = [
                'paths'       => [dirname(__DIR__) . '/Resources/views/form'],
                'form_themes' => ['fields.html.twig'],
            ];
        }

        return $extensionConfigs;
    }
}
