<?php

namespace SimpSpector\Analyser\Tests\Gadget;

use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Gadget\TwigLintGadget;
use SimpSpector\Analyser\Logger\NullLogger;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class TwigLintGadgetTest extends \PHPUnit_Framework_TestCase
{
    public function testNoErrors()
    {
        $path   = __DIR__ . '/_data/twig_lint/success';
        $config = [];

        $gadget = new TwigLintGadget();
        $issues = $gadget->run($path, $config, new NullLogger())->getIssues();

        $this->assertEquals([], $issues);
    }

    public function testOneLineError()
    {
        $path   = __DIR__ . '/_data/twig_lint/error';
        $config = [];

        $gadget = new TwigLintGadget();
        $issues = $gadget->run($path, $config, new NullLogger())->getIssues();

        $expectedIssues = [
            $this->createIssue($path, 'Twig_Error_Syntax: Unclosed "block"', 'one_line_error.html.twig', 11, 'error'),
        ];

        $this->assertEquals($expectedIssues, $issues);
    }

    private function createIssue($path, $message, $file, $line, $level)
    {
        $issue = new Issue($message);
        $issue->setLevel($level);
        $issue->setFile($path . '/' . $file);
        $issue->setLine($line);

        return $issue;
    }
}
