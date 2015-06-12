<?php

namespace SimpSpector\Analyser\Event;

use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class ExecutorResultEvent extends ExecutorEvent
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * @param string $path
     * @param array $config
     * @param Result $result
     * @param AbstractLogger $logger
     */
    public function __construct($path, array $config, Result $result, AbstractLogger $logger)
    {
        parent::__construct($path, $config, $logger);

        $this->result = $result;
    }

    /**
     * @return Result
     */
    public function getResult()
    {
        return $this->result;
    }
}
