<?php

namespace SimpSpector\Analyser\Loader;

use SimpSpector\Analyser\Config\NodeBuilder;
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
        $tree = $this->buildTree();

        $config = $tree->normalize($config);
        $config = $tree->finalize($config);

        return $config;
    }

    /**
     * @return NodeInterface
     */
    private function buildTree()
    {
        $builder = new TreeBuilder();
        $rootNode = $builder->root('simpspector', 'array', new NodeBuilder());
        $children = $rootNode->children();

        foreach ($this->repository->all() as $gadget) {
            $gadgetNode = $children->arrayNode($gadget->getName());
            $gadget->configure($gadgetNode);
        }

        return $builder->buildTree();
    }
}