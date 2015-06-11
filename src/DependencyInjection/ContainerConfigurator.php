<?php

namespace SimpSpector\Analyser\DependencyInjection;

use SimpSpector\Analyser\DependencyInjection\CompilerPass\FormatterCompilerPass;
use SimpSpector\Analyser\DependencyInjection\CompilerPass\GadgetCompilerPass;
use SimpSpector\Analyser\DependencyInjection\CompilerPass\ImporterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class ContainerConfigurator
{
    /**
     * @param ContainerBuilder $container
     */
    public function prepare(ContainerBuilder $container)
    {
        $extension = new SimpSpectorAnalyserExtension();

        $container->registerExtension($extension);
        $container->addCompilerPass(new GadgetCompilerPass());
        $container->addCompilerPass(new FormatterCompilerPass());
        $container->addCompilerPass(new ImporterCompilerPass());
        $container->addCompilerPass(new RegisterListenersPass(
            'simpspector.analyser.event_dispatcher',
            'simpspector.analyser.listener',
            'simpspector.analyser.subscriber'
        ));
        $container->loadFromExtension($extension->getAlias());
    }
}