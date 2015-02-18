<?php

namespace SimpSpector\Analyser\Event;

use SimpSpector\Analyser\Logger\AbstractLogger;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class ExecutorEvent extends Event
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    private $config;

    /**
     * @var AbstractLogger
     */
    private $logger;

    /**
     * @param string $path
     * @param array $config
     * @param AbstractLogger $logger
     */
    public function __construct($path, array $config, AbstractLogger $logger)
    {
        $this->path   = $path;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return AbstractLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}