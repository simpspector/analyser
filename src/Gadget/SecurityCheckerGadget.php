<?php

namespace SimpSpector\Analyser\Gadget;

use DavidBadura\MarkdownBuilder\MarkdownBuilder;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Process\ProcessBuilder;
use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class SecurityCheckerGadget extends AbstractGadget
{
    /**
     * @var string
     */
    private $bin;

    /**
     * @param string $bin
     */
    public function __construct($bin = 'security-checker')
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
                'directory' => './',
                'level'     => Issue::LEVEL_CRITICAL
            ]
        );

        $processBuilder = new ProcessBuilder([$this->bin, 'security:check', '--format=json', $options['directory']]);
        $processBuilder->setWorkingDirectory($path);
        $output = $processBuilder->run($logger);

        $data   = json_decode($output, true);
        $result = new Result();

        if (count($data) == 0) {
            return $result;
        }

        foreach ($data as $lib => $info) {
            $result->merge($this->createIssues(
                trim(rtrim($options['directory'], '/') . '/composer.json', './'),
                $lib,
                $info,
                $options['level']
            ));
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'security-checker';
    }

    /**
     * @param string $composer
     * @param string $lib
     * @param array $info
     * @param string $level
     * @return Result
     */
    private function createIssues($composer, $lib, array $info, $level)
    {
        $result = new Result();

        foreach ($info['advisories'] as $advisory) {
            $result->addIssue($this->createIssue($composer, $lib, $info['version'], $advisory, $level));
        }

        return $result;
    }

    /**
     * @param string $composer
     * @param string $lib
     * @param string $version
     * @param array $advisory
     * @param string $level
     * @return Issue
     */
    private function createIssue($composer, $lib, $version, array $advisory, $level)
    {
        $message = sprintf('package "%s" with the version "%s" have known vulnerabilities', $lib, $version);

        $issue = new Issue($message);

        $issue->setDescription(
            $this->createDescription(
                $advisory['title'],
                $advisory['cve'],
                $advisory['link']
            )
        );

        $issue->setFile($composer);
        $issue->setLevel($level);

        $issue->setExtraInformation(
            [
                'lib'     => $lib,
                'version' => $version,
                'link'    => $advisory['link'],
                'cve'     => $advisory['cve']
            ]
        );

        return $issue;
    }

    /**
     * @param string $title
     * @param string $cve
     * @param string $link
     * @return string
     */
    private function createDescription($title, $cve, $link)
    {
        return sprintf(
            '%s %s',
            (new MarkdownBuilder())->inlineLink($link, $cve . ':'),
            $title
        );
    }
}
