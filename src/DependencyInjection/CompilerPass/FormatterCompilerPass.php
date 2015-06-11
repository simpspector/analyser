<?php

namespace SimpSpector\Analyser\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class FormatterCompilerPass implements CompilerPassInterface
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
        if (!$container->has('simpspector.analyser.formatter')) {
            return;
        }

        $definition     = $container->findDefinition('simpspector.analyser.formatter');
        $taggedServices = $container->findTaggedServiceIds('simpspector.analyser.formatter');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('registerAdapter', array(new Reference($id)));
        }
    }
}