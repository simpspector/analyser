<?php

namespace SimpSpector\Analyser\Config;

use PhpParser\Node\Scalar;
use SimpSpector\Analyser\Issue;
use Symfony\Component\Config\Definition\Builder\EnumNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class LevelNodeDefinition extends EnumNodeDefinition
{
    /**
     * @param string $name
     * @param NodeParentInterface $parent
     */
    public function __construct($name, NodeParentInterface $parent = null)
    {
        parent::__construct($name, $parent);

        $this->values([
            Issue::LEVEL_CRITICAL,
            Issue::LEVEL_ERROR,
            Issue::LEVEL_NOTICE,
            Issue::LEVEL_WARNING
        ]);
    }
}
