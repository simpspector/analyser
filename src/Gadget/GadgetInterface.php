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
     * @param string $path
     * @param array $arguments
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run($path, array $arguments, AbstractLogger $logger);

    /**
     * @return string
     */
    public function getName();
}
