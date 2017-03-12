<?php

namespace SimpSpector\Analyser\Gadget;

use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;
use SimpSpector\Analyser\Struct\ConfigurationFile;
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
     * returns a short identifier for this gadget
     * used as configuration key in .simpspector.yml
     * e.g. phpmd
     *
     * @return string
     */
    public function getName();

    /**
     * more elaborate gadget name to be displayed in
     * init command or the web interface
     *
     * e.g. PHP Mess Detector
     *
     * @return string
     */
    public function getDescription();

    /**
     * @return ConfigurationFile|null
     */
    public function getDefaultConfigurationFile();
}
