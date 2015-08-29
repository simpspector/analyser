<?php

namespace SimpSpector\Analyser\Loader;

use SimpSpector\Analyser\Config\NodeBuilder;
use SimpSpector\Analyser\Config\TreeFactory;
use SimpSpector\Analyser\Exception\MissingSimpSpectorConfigException;
use SimpSpector\Analyser\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class DefaultConfigLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;
    /**
     * @var string
     */
    private $defaultConfigPath;

    /**
     * @param LoaderInterface $loader
     * @param string $default
     */
    public function __construct(LoaderInterface $loader, $defaultConfigPath)
    {
        $this->loader            = $loader;
        $this->defaultConfigPath = $defaultConfigPath;
    }

    /**
     * @param string $path
     * @return array
     */
    public function load($path)
    {
        try {
            return $this->loader->load($path);
        } catch (MissingSimpSpectorConfigException $e) {
            return $this->loader->load($this->defaultConfigPath);
        }
    }
}
