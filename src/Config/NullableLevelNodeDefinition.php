<?php

namespace SimpSpector\Analyser\Config;

use PhpParser\Node\Scalar;
use SimpSpector\Analyser\Issue;
use Symfony\Component\Config\Definition\Builder\EnumNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;

class NullableLevelNodeDefinition extends EnumNodeDefinition
{
    /**
     * @param string $name
     * @param NodeParentInterface $parent
     */
    public function __construct($name, NodeParentInterface $parent = null)
    {
        parent::__construct($name, $parent);

        $this->values([
            null,
            Issue::LEVEL_CRITICAL,
            Issue::LEVEL_ERROR,
            Issue::LEVEL_WARNING,
            Issue::LEVEL_NOTICE
        ]);
    }
}
