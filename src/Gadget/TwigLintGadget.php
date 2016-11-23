<?php

namespace SimpSpector\Analyser\Gadget;

use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 * @author David Badura <d.a.badura@gmail.com>
 */
class TwigLintGadget extends AbstractGadget
{
    /**
     * @var string
     */
    private $bin;

    /**
     * @param string $bin
     */
    public function __construct($bin = 'twig-lint')
    {
        $this->bin = $bin;
    }

    /**
     * @param string $path
     * @param array $arguments
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run($path, array $arguments, AbstractLogger $logger)
    {
        if (!$arguments) {
            $arguments = ['lint', './src'];
        }

        $output = $this->execute($path, array_merge([$this->bin, '--format=csv'], $arguments), $logger, [0, 1]);
        $array = $this->convertToArray($output);

        $result = new Result();

        foreach ($array as $error) {
            $issue = new Issue($this, $error['message']);
            $issue->setLevel(Issue::LEVEL_CRITICAL);
            $issue->setFile($error['file']);
            $issue->setLine($error['line']);

            $result->addIssue($issue);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'twig-lint';
    }

    /**
     * @param string $csv
     * @return array
     */
    private function convertToArray($csv)
    {
        $lines = explode(PHP_EOL, $csv);

        $header = ['file', 'line', 'message'];

        $result = [];
        foreach ($lines as $line) {
            if (!$line) {
                continue;
            }

            $result[] = array_combine($header, str_getcsv($line));
        }

        return $result;
    }
}
