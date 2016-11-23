<?php

namespace SimpSpector\Analyser\Event;

use SimpSpector\Analyser\Gadget\GadgetInterface;
use SimpSpector\Analyser\Logger\AbstractLogger;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GadgetEvent extends Event
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var GadgetInterface
     */
    protected $gadget;

    /**
     * @var AbstractLogger
     */
    private $logger;

    /**
     * @param string $path
     * @param array $arguments
     * @param GadgetInterface $gadget
     * @param AbstractLogger $logger
     */
    public function __construct($path, array $arguments, GadgetInterface $gadget, AbstractLogger $logger)
    {
        $this->path = $path;
        $this->arguments = $arguments;
        $this->gadget = $gadget;
        $this->logger = $logger;
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
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return GadgetInterface
     */
    public function getGadget()
    {
        return $this->gadget;
    }

    /**
     * @return AbstractLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
