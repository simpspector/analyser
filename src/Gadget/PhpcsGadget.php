<?php

namespace SimpSpector\Analyser\Gadget;

use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class PhpcsGadget extends AbstractGadget
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
     * @param array $arguments
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run($path, array $arguments, AbstractLogger $logger)
    {
        if (empty($arguments)) {
            $arguments[] = 'src/';
        }

        $defaultConfigFile = $path . '/' . $this->getDefaultConfigurationFile()->filename;
        if (file_exists($defaultConfigFile) && ! $this->argumentsContain($arguments, '--standard=')) {
            $arguments[] = '--standard=' . $this->getDefaultConfigurationFile()->filename;
        }

        $output = $this->execute($path, array_merge([$this->bin, '--report=csv'], $arguments), $logger, [0, 1]);

        $rawIssues = $this->convertFromCsvToArray($output);

        $result = new Result();
        foreach ($rawIssues as $info) {
            $result->addIssue($this->createIssue($info));
        }

        return $result;
    }

    /**
     * @see GadgetInterface::getName()
     */
    public function getName()
    {
        return 'phpcs';
    }

    /**
     * @see GadgetInterface::getDescription()
     */
    public function getDescription()
    {
        return 'PHP CodeSniffer';
    }

    /**
     * @see GadgetInterface::getDefaultConfigurationFile()
     */
    public function getDefaultConfigurationFile()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<!-- for more information visit https://github.com/squizlabs/PHP_CodeSniffer/wiki/Annotated-ruleset.xml -->
<ruleset name="Simpspector PHPCS Template">
    <description>The PSR-2 coding standard.</description>
    <rule ref="PSR2"/>

    <file>src</file>
</ruleset>
XML;

        return new ConfigurationFile('.phpcs.xml', $xml);
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
