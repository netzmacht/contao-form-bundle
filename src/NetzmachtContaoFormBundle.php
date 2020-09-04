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

namespace Netzmacht\ContaoFormBundle;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\AddTaggedServicesAsArgumentPass;
use Netzmacht\ContaoFormBundle\DependencyInjection\Compiler\TokenStoragePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NetzmachtContaoFormBundle
 */
final class NetzmachtContaoFormBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
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
