<?php

namespace SimpSpector\Analyser\Config;

use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class PathNodeDefinition extends ScalarNodeDefinition
{
    /**
     * @param string $name
     * @param NodeParentInterface $parent
     */
    public function __construct($name, NodeParentInterface $parent = null)
    {
        parent::__construct($name, $parent);

        $this->validate()
            ->ifTrue(function ($value) {
                $value = trim($value);

                return strpos($value, '..') !== false || strpos($value, '/') === 0;
            })
            ->thenInvalid('path is not allowed');
    }
}
