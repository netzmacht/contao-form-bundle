<?php

/**
 * contao-form-bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0
 * @filesource
 */

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

/**
 * Class Plugin
 */
class Plugin implements BundlePluginInterface, ExtensionPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(NetzmachtContaoFormBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, NetzmachtContaoToolkitBundle::class])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionConfig($extensionName, array $extensionConfigs, ContainerBuilder $container)
    {
        if ($extensionName === 'framework') {
            $extensionConfigs[] = [
                'form' => true
            ];
        }

        return $extensionConfigs;
    }
}
