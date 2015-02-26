<?php

namespace SimpSpector\Analyser;

use SimpSpector\Analyser\Gadget\GadgetInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Issue
{
    const LEVEL_NOTICE   = 'notice';
    const LEVEL_WARNING  = 'warning';
    const LEVEL_ERROR    = 'error';
    const LEVEL_CRITICAL = 'critical';

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $gadget;

    /**
     * @var string
     */
    private $level;

    /**
     * @var string|null
     */
    private $file;

    /**
     * @var int|null
     */
    private $line;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $extraInformation;

    /**
     * @param GadgetInterface $gadget
     * @param string $message
     */
    public function __construct(GadgetInterface $gadget, $message)
    {
        $this->gadget           = $gadget->getName();
        $this->message          = $message;
        $this->level            = self::LEVEL_NOTICE;
        $this->extraInformation = [];
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getGadget()
    {
        return $this->gadget;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param string $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return null|string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param null|string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return int|null
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param int|null $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getExtraInformation()
    {
        return $this->extraInformation;
    }

    /**
     * @param array $extraInformation
     */
    public function setExtraInformation($extraInformation)
    {
        $this->extraInformation = $extraInformation;
    }
}