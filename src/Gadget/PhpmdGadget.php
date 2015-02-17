<?php

namespace SimpSpector\Analyser\Gadget;

use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Process\ProcessBuilder;
use SimpSpector\Analyser\Result;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class PhpmdGadget extends AbstractGadget
{
    const NAME = 'phpmd';

    /**
     * @var string
     */
    private $bin;

    /**
     * @param string $bin
     */
    public function __construct($bin = 'phpmd')
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
        $options = $this->prepareOptions(
            $options,
            [
                'files'    => './',
                'rulesets' => ['codesize', 'unusedcode']
            ],
            ['files', 'rulesets']
        );

        $processBuilder = new ProcessBuilder([$this->bin]);
        $processBuilder->add(implode(',', $options['files']));
        $processBuilder->add('xml');
        $processBuilder->add(implode(',', $options['rulesets']));
        $processBuilder->setWorkingDirectory($path);
        $output = $processBuilder->run($logger);

        $data = $this->convertFromXmlToArray($output);

        $result = new Result();

        if (!isset($data['file']) || !is_array($data['file'])) {
            return $result;
        }

        $files = (isset($data['file'][0])) ? $data['file'] : [$data['file']];
        foreach ($files as $file) {
            $violations = (isset($file['violation'][0])) ? $file['violation'] : [$file['violation']];

            foreach ($violations as $violation) {
                $result->addIssue($this->createIssue($path, $file['@name'], $violation));
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param string $xml
     * @return array
     */
    private function convertFromXmlToArray($xml)
    {
        $encoder = new XmlEncoder('pmd');

        return $encoder->decode($xml, 'xml');
    }

    /**
     * @param string $path
     * @param string $file
     * @param array $data
     * @return Issue
     */
    private function createIssue($path, $file, array $data)
    {
        $issue = new Issue(trim($data['#']), self::NAME, Issue::LEVEL_WARNING);
        $issue->setFile($this->cleanupFilePath($path, $file));
        $issue->setLine($data['@beginline']);

        $issue->setExtraInformation(
            [
                'rule'            => $data['@rule'],
                'ruleset'         => $data['@ruleset'],
                'externalInfoUrl' => $data['@externalInfoUrl'],
                'priority'        => $data['@priority']
            ]
        );

        return $issue;
    }
}