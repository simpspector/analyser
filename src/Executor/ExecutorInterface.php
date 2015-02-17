<?php

namespace SimpSpector\Analyser\Executor;

use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface ExecutorInterface
{
    /**
     * @param string $path
     * @param array $config
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run($path, array $config, AbstractLogger $logger = null);
}
