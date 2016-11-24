<?php

namespace SimpSpector\Analyser\Exception;

use Exception;

/**
 * @author David Badura <d.badura@gmx.de>
 */
class GadgetProcessException extends \Exception
{
    /**
     * @var string
     */
    private $standardOutput;

    /**
     * @var string
     */
    private $errorOutput;

    /**
     * @param string $commandLine
     * @param int $statusCode
     * @param string $standardOutput
     * @param string $errorOutput
     */
    public function __construct($commandLine, $statusCode, $standardOutput, $errorOutput)
    {
        $message = sprintf('%s %s', $commandLine, $statusCode);

        parent::__construct($message, $statusCode);

        $this->standardOutput = $standardOutput;
        $this->errorOutput = $errorOutput;
    }

    /**
     * @return string
     */
    public function getStandardOutput()
    {
        return $this->standardOutput;
    }

    /**
     * @return string
     */
    public function getErrorOutput()
    {
        return $this->errorOutput;
    }
}
