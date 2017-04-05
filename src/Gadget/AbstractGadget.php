<?php

namespace SimpSpector\Analyser\Gadget;

use SimpSpector\Analyser\Exception\GadgetProcessException;
use SimpSpector\Analyser\Logger\AbstractLogger;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
abstract class AbstractGadget implements GadgetInterface
{
    /**
     * @param string $path
     * @param array $args
     * @param AbstractLogger $logger
     * @param array $allowedStatusCodes
     * @return string
     */
    protected function execute($path, array $args, AbstractLogger $logger, array $allowedStatusCodes = [0])
    {
        $processBuilder = new ProcessBuilder($args);
        $processBuilder->setWorkingDirectory($path);

        $process = $processBuilder->getProcess();
        $process->setTimeout(3600);

        $logger->writeln();
        $logger->writeln('CMD > ' . $process->getCommandLine());
        $logger->writeln();

        $process->run(
            function ($type, $buffer) use ($logger) {
                $logger->write($buffer);
            }
        );

        if (!in_array($process->getExitCode(), $allowedStatusCodes, true)) {
            throw new GadgetProcessException(
                $process->getCommandLine(),
                $process->getExitCode(),
                $process->getOutput(),
                $process->getErrorOutput()
            );
        }

        return $process->getOutput();
    }

    /**
     * @param array $arguments
     * @param string $substring
     *
     * @return bool
     */
    protected function argumentsContain(array $arguments, $substring)
    {
        foreach ($arguments as $argument) {
            if (stristr($argument, $substring) !== false) {
                return true;
            }
        }

        return false;
    }
}
