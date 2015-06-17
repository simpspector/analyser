<?php

namespace SimpSpector\Analyser;

use SimpSpector\Analyser\Gadget\GadgetInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Issue
{
    const LEVEL_NOTICE = 'notice';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_CRITICAL = 'critical';

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private $title;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private $gadget;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private $level;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     */
    private $file;

    /**
     * @var int|null
     *
     * @Serializer\Type("integer")
     */
    private $line;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private $description;

    /**
     * @var array
     *
     * @Serializer\Type("array")
     */
    private $extraInformation;

    /**
     * @param GadgetInterface $gadget
     * @param string $title
     */
    public function __construct(GadgetInterface $gadget, $title)
    {
        $this->gadget           = $gadget->getName();
        $this->title            = $title;
        $this->level            = self::LEVEL_NOTICE;
        $this->extraInformation = [];
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
