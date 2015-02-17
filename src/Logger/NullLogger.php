<?php

namespace SimpSpector\Analyser\Logger;

/**
 * @author David Badura <badura@simplethings.de>
 */
class NullLogger extends AbstractLogger
{
    /**
     * @param $message
     */
    public function write($message)
    {
        // do nothing
    }
}