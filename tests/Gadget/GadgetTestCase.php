<?php

namespace SimpSpector\Analyser\Tests\Gadget;

use SimpSpector\Analyser\Config\NodeBuilder;
use SimpSpector\Analyser\Gadget\GadgetInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
abstract class GadgetTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param GadgetInterface $gadget
     * @param array $expected
     * @param array $config
     */
    protected function assertConfig(GadgetInterface $gadget, array $expected, array $config)
    {
        $this->assertEquals($expected, $this->resolve($gadget, $config));
    }

    /**
     * @param GadgetInterface $gadget
     * @param array $config
     * @return array
     */
    protected function resolve(GadgetInterface $gadget, array $config)
    {
        $builder = new TreeBuilder();
        $node = $builder->root($gadget->getName(), 'array', new NodeBuilder());

        $gadget->configure($node);

        $tree = $builder->buildTree();

        $config = $tree->normalize($config);
        return $tree->finalize($config);
    }
} 
