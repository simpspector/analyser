<?php

namespace SimpSpector\Analyser\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GadgetCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('simpspector.analyser.repository')) {
            return;
        }

        $definition     = $container->findDefinition('simpspector.analyser.repository');
        $taggedServices = $container->findTaggedServiceIds('simpspector.analyser.gadget');

        foreach (array_keys($taggedServices) as $id) {
            $definition->addMethodCall('add', array(new Reference($id)));
        }
    }
}
