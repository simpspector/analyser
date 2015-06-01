<?php

namespace SimpSpector\Analyser\Loader;

use SimpSpector\Analyser\Config\NodeBuilder;
use SimpSpector\Analyser\Config\TreeFactory;
use SimpSpector\Analyser\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class ConfigLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @param LoaderInterface $loader
     * @param RepositoryInterface $repository
     */
    public function __construct(LoaderInterface $loader, RepositoryInterface $repository)
    {
        $this->loader     = $loader;
        $this->repository = $repository;
    }

    /**
     * @param string $path
     * @return array
     */
    public function load($path)
    {
        $config = $this->loader->load($path);
        $tree = (new TreeFactory())->createTree($this->repository);

        $config = $tree->normalize($config);
        $config = $tree->finalize($config);

        return $config;
    }
}