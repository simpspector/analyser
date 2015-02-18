<?php

namespace SimpSpector\Analyser\Gadget;

use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;
use SimpSpector\Analyser\Util\FilesystemHelper;

/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
class CommentBlacklistGadget extends AbstractGadget
{
    const NAME = 'comment_blacklist';

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
                'files'     => './',
                'blacklist' => [
                    'todo'        => Issue::LEVEL_NOTICE,
                    'dont commit' => Issue::LEVEL_ERROR,
                ]
            ],
            ['files', 'blacklist']
        );

        $result = new Result();

        foreach (FilesystemHelper::findFiles($path, $options['files']) as $filename) {
            $result->merge($this->processFile($filename, $options));
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
     * @param string $filename
     * @param array $options
     * @return Result
     */
    private function processFile($filename, array $options)
    {
        $comments = $this->extract($filename);

        $result = new Result();
        foreach ($comments as $comment) {
            $result->merge($this->processComment($filename, $options, $comment));
        }

        return $result;
    }

    /**
     * @param string $filename
     * @return array
     */
    private function extract($filename)
    {
        $allTokens     = token_get_all(file_get_contents($filename));
        $commentTokens = array_filter(
            $allTokens,
            function ($token) {
                return (count($token) === 3) && (in_array(
                    $token[0],
                    [T_COMMENT, T_DOC_COMMENT]
                ));
            }
        );

        return array_map(
            function ($comment) {
                return [
                    'content' => $comment[1],
                    'line'    => $comment[2],
                ];
            },
            $commentTokens
        );
    }

    /**
     * @param string $filename
     * @param array $options
     * @param string $comment
     * @return Result
     */
    private function processComment($filename, array $options, $comment)
    {
        $result = new Result();

        foreach (explode("\n", $comment['content']) as $lineOffset => $line) {
            foreach ($options['blacklist'] as $blacklistedWord => $errorLevel) {

                if (stristr($line, $blacklistedWord) === false) {
                    continue;
                }

                $issue = new Issue(sprintf('found "%s" in a comment', $blacklistedWord), $this->getName(), $errorLevel);
                $issue->setFile($filename);
                $issue->setLine($comment['line'] + $lineOffset);

                $result->addIssue($issue);
            }
        }

        return $result;
    }
}
