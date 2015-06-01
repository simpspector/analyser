<?php

namespace SimpSpector\Analyser\Config;

use SimpSpector\Analyser\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class ReferenceDumper
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $gadget
     * @return string
     * @throws \Exception
     */
    public function dump($gadget = null)
    {
        /** @var ArrayNode $tree */
        $node = (new TreeFactory())->createTree($this->repository);

        if ($gadget) {
            $children = $node->getChildren();

            if (!isset($children[$gadget])) {
                throw new \Exception(sprintf('gadget "%s" not exists', $gadget));
            }

            $node = $children[$gadget];
        }

        $dumper = new YamlReferenceDumper();

        return $dumper->dumpNode($node);
    }
}