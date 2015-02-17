<?php

namespace SimpSpector\Analyser\Logger;

/**
 * @author David Badura <badura@simplethings.de>
 */
class FileLogger extends AbstractLogger
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var resource
     */
    private $handler;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->handler = fopen($this->file, 'a');
    }

    /**
     * @param $message
     */
    public function write($message)
    {
        fwrite($this->handler, $message);
    }
} 