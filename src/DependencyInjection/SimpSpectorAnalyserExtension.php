<?php

namespace SimpSpector\Analyser\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Webmozart\PathUtil\Path;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class SimpSpectorAnalyserExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $path = __DIR__ . '/../../config';

        $loader = new YamlFileLoader(
            $container,
            new FileLocator($path)
        );

        $loader->load('parameters.dist.yml');

        if (file_exists(Path::join($path, 'parameters.yml'))) {
            $loader->load('parameters.yml');
        }

        $loader->load('services.yml');
    }

    public function getAlias()
    {
        return 'simpspector_analyser';
    }
}
