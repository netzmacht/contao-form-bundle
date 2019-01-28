<?php

/**
 * Netzmacht Contao Form Bundle.
 *
 * @package    contao-form-bundle
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-form-bundle/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoFormBundle\ContaoManager;

use AdamQuaile\Bundle\FieldsetBundle\AdamQuaileFieldsetBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\ManagerPlugin\Config\ExtensionPluginInterface;
use Netzmacht\Contao\Toolkit\Bundle\NetzmachtContaoToolkitBundle;
use Netzmacht\ContaoFormBundle\NetzmachtContaoFormBundle;
use function dirname;

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
            BundleConfig::create(AdamQuaileFieldsetBundle::class),
            BundleConfig::create(NetzmachtContaoFormBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, NetzmachtContaoToolkitBundle::class])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionConfig($extensionName, array $extensionConfigs, ContainerBuilder $container): array
    {
        if ($extensionName === 'framework') {
            $extensionConfigs[] = [
                'form' => true
            ];
        }

        if ($extensionName === 'twig') {
            $extensionConfigs[] = [
                'form_themes' => [dirname(__DIR__) . '/Resources/views/form/fields.html.twig']
            ];
        }

        return $extensionConfigs;
    }
}
