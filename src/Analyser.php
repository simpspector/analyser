<?php

namespace SimpSpector\Analyser;

use SimpSpector\Analyser\Executor\ExecutorInterface;
use SimpSpector\Analyser\Loader\LoaderInterface;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Logger\NullLogger;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Analyser
{
    /**
     * @var ExecutorInterface
     */
    protected $executor;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @param ExecutorInterface $executor
     * @param LoaderInterface   $loader
     */
    public function __construct(ExecutorInterface $executor, LoaderInterface $loader)
    {
        $this->executor = $executor;
        $this->loader   = $loader;
    }

    /**
     * @param string         $path
     * @param string|null    $configFile
     * @param AbstractLogger $logger
     * @return Result
     */
    public function analyse($path, $configFile = null, AbstractLogger $logger = null)
    {
        $logger = $logger ?: new NullLogger();
        $path   = rtrim($path, '/');

        if (!$configFile) {
            $configFile = $path . '/.simpspector.yml';
        }

        $config = $this->loader->load($configFile);

        return $this->executor->run($path, $config, $logger);
    }

    /**
     * @return ExecutorInterface
     */
    public function getExecutor()
    {
        return $this->executor;
    }

    /**
     * @return LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }
}
