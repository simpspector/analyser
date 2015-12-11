<?php

namespace SimpSpector\Analyser\Tests\Gadget;

use SimpSpector\Analyser\Gadget\GadgetInterface;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Gadget\TwigLintGadget;
use SimpSpector\Analyser\Logger\NullLogger;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class TwigLintGadgetTest extends GadgetTestCase
{
    public function testDefaultConfig()
    {
        $gadget = new TwigLintGadget();

        $this->assertConfig($gadget, ['files' => ['./'], 'error_level' => 'error'], []);
    }

    public function testNoErrors()
    {
        $path = __DIR__ . '/_data/twig_lint/success';

        $gadget = new TwigLintGadget();
        $config = $this->resolve($gadget, []);

        $issues = $gadget->run($path, $config, new NullLogger())->getIssues();

        $this->assertEquals([], $issues);
    }

    public function testOneLineError()
    {
        $path = __DIR__ . '/_data/twig_lint/error';

        $gadget = new TwigLintGadget();
        $config = $this->resolve($gadget, []);

        $issues = $gadget->run($path, $config, new NullLogger())->getIssues();
        
        if (version_compare(\Twig_Environment, '1.23.1') >= 0) {
            $msg = 'Twig_Error_Syntax: Unclosed "block".';
        } else {
            $msg = 'Twig_Error_Syntax: Unclosed "block"';
        }

        $expectedIssues = [$this->createIssue($gadget, $path, $msg, 'one_line_error.html.twig', 11, 'error'),];

        $this->assertEquals($expectedIssues, $issues);
    }

    private function createIssue(GadgetInterface $gadget, $path, $message, $file, $line, $level)
    {
        $issue = new Issue($gadget, $message);
        $issue->setLevel($level);
        $issue->setFile($path . '/' . $file);
        $issue->setLine($line);

        return $issue;
    }
}
