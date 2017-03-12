<?php

namespace SimpSpector\Analyser\Gadget;

use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Process\ProcessBuilder;
use SimpSpector\Analyser\Result;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class PhpmdGadget extends AbstractGadget
{
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
     * @param array $arguments
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run($path, array $arguments, AbstractLogger $logger)
    {
        if (!isset($arguments[0])) {
            $arguments[0] = './src';
        }

        $arguments[1] = 'xml';

        if (!isset($arguments[2])) {
            $arguments[2] = 'codesize,unusedcode,naming';
        }

        $output = $this->execute($path, array_merge([$this->bin], $arguments), $logger, [0, 2]);
        $data = $this->convertFromXmlToArray($output);

        $result = new Result();

        if (!isset($data['file']) || !is_array($data['file'])) {
            return $result;
        }

        $files = (isset($data['file'][0])) ? $data['file'] : [$data['file']];
        foreach ($files as $file) {
            $violations = (isset($file['violation'][0])) ? $file['violation'] : [$file['violation']];

            foreach ($violations as $violation) {
                $result->addIssue($this->createIssue($file['@name'], $violation));
            }
        }

        return $result;
    }

    /**
     * @see GadgetInterface::getName()
     */
    public function getName()
    {
        return 'phpmd';
    }

    /**
     * @see GadgetInterface::getDescription()
     */
    public function getDescription()
    {
        return 'PHP Mess Detector';
    }

    /**
     * @see GadgetInterface::getDefaultConfigurationFile()
     */
    public function getDefaultConfigurationFile()
    {
        return null;
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
     * @param string $file
     * @param array $data
     * @return Issue
     */
    private function createIssue($file, array $data)
    {
        $issue = new Issue($this, trim($data['#']));
        $issue->setLevel(Issue::LEVEL_WARNING);
        $issue->setFile($file);
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
