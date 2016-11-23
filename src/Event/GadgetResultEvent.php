<?php

namespace SimpSpector\Analyser\Event;

use SimpSpector\Analyser\Gadget\GadgetInterface;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GadgetResultEvent extends GadgetEvent
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * @param string $path
     * @param array $arguments
     * @param GadgetInterface $gadget
     * @param Result $result
     * @param AbstractLogger $logger
     */
    public function __construct($path, array $arguments, GadgetInterface $gadget, Result $result, AbstractLogger $logger)
    {
        parent::__construct($path, $arguments, $gadget, $logger);

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
