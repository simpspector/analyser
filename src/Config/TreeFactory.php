<?php

namespace SimpSpector\Analyser\Config;

use SimpSpector\Analyser\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class TreeFactory
{
    /**
     * @param RepositoryInterface $repository
     * @return NodeInterface
     */
    public function createTree(RepositoryInterface $repository)
    {
        $builder  = new TreeBuilder();
        $rootNode = $builder->root('simpspector', 'array', new NodeBuilder());
        $children = $rootNode->children();

        foreach ($repository->all() as $gadget) {
            $gadgetNode = $children->arrayNode($gadget->getName());
            $gadget->configure($gadgetNode);
        }

        return $builder->buildTree();
    }
}
