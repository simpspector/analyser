<?php

namespace SimpSpector\Analyser\Logger;

use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author David Badura <badura@simplethings.de>
 */
class ConsoleLogger extends AbstractLogger
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param $message
     */
    public function write($message)
    {
        if ($this->output instanceof Output && !$this->output->isVerbose()) {
            $this->output->write('.');
        } else {
            $this->output->write($message);
        }
    }
}
