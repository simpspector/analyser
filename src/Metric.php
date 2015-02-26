<?php

namespace SimpSpector\Analyser;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Metric
{
    /**
     *
     */
    const TYPE_COUNT = 'count';

    /**
     *  from 0-100
     */
    const TYPE_RATING = 'rating';

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $value;

    /**
     * @var string
     */
    private $description;

    /**
     * @param string $title
     * @param string $code
     * @param int $value
     * @param string $type
     */
    public function __construct($title, $code, $value, $type = self::TYPE_RATING)
    {
        $this->title  = $title;
        $this->code   = $code;
        $this->type   = $type;
        $this->value  = $value;
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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