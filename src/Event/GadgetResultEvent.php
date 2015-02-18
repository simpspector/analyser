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
     * @param array $options
     * @param GadgetInterface $gadget
     * @param Result $result
     * @param AbstractLogger $logger
     */
    public function __construct($path, array $options, GadgetInterface $gadget, Result $result, AbstractLogger $logger)
    {
        parent::__construct($path, $options, $gadget, $logger);

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