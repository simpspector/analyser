<?php

namespace SimpSpector\Analyser\Config;

use Symfony\Component\Config\Definition\Builder\NodeBuilder as BaseBuilder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class NodeBuilder extends BaseBuilder
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->nodeMapping['path']           = __NAMESPACE__ . '\\PathNodeDefinition';
        $this->nodeMapping['paths']          = __NAMESPACE__ . '\\PathsNodeDefinition';
        $this->nodeMapping['level']          = __NAMESPACE__ . '\\LevelNodeDefinition';
        $this->nodeMapping['nullable_level'] = __NAMESPACE__ . '\\NullableLevelNodeDefinition';
        $this->nodeMapping['level_map']      = __NAMESPACE__ . '\\LevelMapNodeDefinition';
    }
}
