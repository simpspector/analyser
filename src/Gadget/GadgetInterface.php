<?php

namespace SimpSpector\Analyser\Gadget;

use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface GadgetInterface
{
    /**
     * @param ArrayNodeDefinition $node
     */
    public function configure(ArrayNodeDefinition $node);

    /**
     * @param string $path
     * @param array $options
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run($path, array $options, AbstractLogger $logger);

    /**
     * @return string
     */
    public function getName();
}