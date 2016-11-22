<?php

namespace SimpSpector\Analyser\Gadget;

use SimpSpector\Analyser\Exception\MissingConfigFileException;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Process\ProcessBuilder;
use SimpSpector\Analyser\Result;
use Webmozart\PathUtil\Path;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class PhpcsGadget implements GadgetInterface
{
    /**
     * @var string
     */
    private $bin;

    /**
     * @param string $bin
     */
    public function __construct($bin = 'phpcs')
    {
        $this->bin = $bin;
    }

    /**
     * @param string $path
     * @param array $options
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run($path, array $options, AbstractLogger $logger)
    {
        if (!file_exists(Path::join($path, 'phpcs.xml')) && !file_exists(Path::join($path, 'phpcs.xml.dist'))) {
            throw new MissingConfigFileException();
        }

        $processBuilder = new ProcessBuilder([$this->bin, '--report=csv']);
        $processBuilder->setWorkingDirectory($path);

        $output = $processBuilder->run($logger);

        $rawIssues = $this->convertFromCsvToArray($output);

        $result = new Result();
        foreach ($rawIssues as $info) {
            $result->addIssue($this->createIssue($info));
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phpcs';
    }

    /**
     * @param string $csv
     * @return array
     */
    private function convertFromCsvToArray($csv)
    {
        $lines = explode(PHP_EOL, $csv);

        $header = array_map('strtolower', str_getcsv(array_shift($lines)));

        $result = [];
        foreach ($lines as $line) {
            if (!$line) {
                continue;
            }

            $result[] = array_combine($header, str_getcsv($line));
        }

        return $result;
    }

    /**
     * @param array $data
     * @return Issue
     */
    private function createIssue(array $data)
    {
        $issue = new Issue($this, $data['message']);
        $issue->setFile($data['file']);
        $issue->setLine($data['line']);

        switch ($data['type']) {
            case 'error':
                $issue->setLevel(Issue::LEVEL_ERROR);
                break;
            case 'warning':
                $issue->setLevel(Issue::LEVEL_WARNING);
                break;
        }

        $issue->setExtraInformation(
            [
                'source' => $data['source'],
                'severity' => $data['severity'],
                'column' => $data['column']
            ]
        );

        return $issue;
    }
}
