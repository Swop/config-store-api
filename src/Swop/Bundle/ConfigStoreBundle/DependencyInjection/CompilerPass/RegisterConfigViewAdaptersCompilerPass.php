<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Swop\Bundle\ConfigStoreBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterConfigViewAdaptersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swop.config_store.config_view.config_view_dumper')) {
            return;
        }

        $definition = $container->getDefinition(
            'swop.config_store.config_view.config_view_dumper'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'swop.bundle.config_store.config_view_dumper.adapter'
        );

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'registerAdapter',
                array(new Reference($id))
            );
        }
    }
}
