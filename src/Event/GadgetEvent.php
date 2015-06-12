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
    protected $options;

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
     * @param array $options
     * @param GadgetInterface $gadget
     * @param AbstractLogger $logger
     */
    public function __construct($path, array $options, GadgetInterface $gadget, AbstractLogger $logger)
    {
        $this->path    = $path;
        $this->options = $options;
        $this->gadget  = $gadget;
        $this->logger  = $logger;
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
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
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
