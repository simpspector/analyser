<?php

namespace SimpSpector\Analyser\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class ImporterCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('simpspector.analyser.importer')) {
            return;
        }

        $definition     = $container->findDefinition('simpspector.analyser.importer');
        $taggedServices = $container->findTaggedServiceIds('simpspector.analyser.importer');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('registerAdapter', array(new Reference($id)));
        }
    }
}