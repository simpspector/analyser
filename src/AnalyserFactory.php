<?php

namespace SimpSpector\Analyser;

use SimpSpector\Analyser\DependencyInjection\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class AnalyserFactory
{
    public function create()
    {
        $container = new ContainerBuilder();
        $container->setParameter('simpspector.analyser.bin', __DIR__ . '/../bin');

        (new ContainerConfigurator())->prepare($container);

        $container->compile();

        return $container->get('simpspector.analyser');
    }
}
