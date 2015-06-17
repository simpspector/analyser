<?php

namespace SimpSpector\Analyser;

use JMS\Serializer\Annotation as Serializer;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Metric
{
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
    private $code;

    /**
     * @var float
     *
     * @Serializer\Type("float")
     */
    private $value;

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private $description;

    /**
     * @param string $title
     * @param string $code
     * @param int $value
     */
    public function __construct($title, $code, $value)
    {
        $this->title = $title;
        $this->code  = $code;
        $this->value = $value;
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
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
}
