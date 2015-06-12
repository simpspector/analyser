<?php

namespace SimpSpector\Analyser\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class PathsNodeDefinition extends ArrayNodeDefinition
{
    /**
     * @param string $name
     * @param NodeParentInterface $parent
     */
    public function __construct($name, NodeParentInterface $parent = null)
    {
        parent::__construct($name, $parent);

        $this->nodeBuilder = new NodeBuilder();
        $this->prototype('path');
        $this->beforeNormalization()->always(function ($value) {

            if (!is_array($value)) {
                $value = [$value];
            }

            return $value;
        });
    }
}
