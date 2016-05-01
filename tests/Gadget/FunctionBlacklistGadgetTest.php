<?php

namespace SimpSpector\Analyser\Tests\Gadget;

use SimpSpector\Analyser\Gadget\FunctionBlacklistGadget;
use SimpSpector\Analyser\Gadget\GadgetInterface;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Logger\NullLogger;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class FunctionBlacklistGadgetTest extends GadgetTestCase
{
    private function createIssue(GadgetInterface $gadget, $path, $message, $line, $level)
    {
        $issue = new Issue($gadget, $message);
        $issue->setLevel($level);
        $issue->setFile($path . '/foo.php');
        $issue->setLine($line);

        return $issue;
    }

    public function testDefaultConfig()
    {
        $gadget = new FunctionBlacklistGadget();

        $this->assertConfig($gadget, [
            'files' => ['./'],
            'blacklist' => [
                'die' => 'error',
                'var_dump' => 'error',
                'echo' => 'warning',
                'dump' => 'error'
            ]
        ], []);
    }

    public function testDefault()
    {
        $path = __DIR__ . '/_data/function_blacklist';

        $gadget = new FunctionBlacklistGadget();
        $config = $this->resolve($gadget, []);

        $issues = $gadget->run($path, $config, new NullLogger())->getIssues();

        $expectedIssues = [
            $this->createIssue($gadget, $path, 'function / statement "echo" is blacklisted', 9, Issue::LEVEL_WARNING),
            $this->createIssue($gadget, $path, 'function / statement "echo" is blacklisted', 13, Issue::LEVEL_WARNING),
            $this->createIssue($gadget, $path, 'function / statement "var_dump" is blacklisted', 32,
                Issue::LEVEL_ERROR),
            $this->createIssue($gadget, $path, 'function / statement "die/exit" is blacklisted', 37,
                Issue::LEVEL_ERROR),
            $this->createIssue($gadget, $path, 'function / statement "die/exit" is blacklisted', 39,
                Issue::LEVEL_ERROR),
            $this->createIssue($gadget, $path, 'function / statement "var_dump" is blacklisted', 46,
                Issue::LEVEL_ERROR),
        ];

        $this->assertEquals($expectedIssues, $issues);
    }

    public function testDie()
    {
        $path = __DIR__ . '/_data/function_blacklist';

        $gadget = new FunctionBlacklistGadget();
        $config = $this->resolve($gadget, ['blacklist' => ['die' => 'critical']]);

        $issues = $gadget->run($path, $config, new NullLogger())->getIssues();

        $expectedIssues = [
            $this->createIssue($gadget, $path, 'function / statement "die/exit" is blacklisted', 37,
                Issue::LEVEL_CRITICAL),
            $this->createIssue($gadget, $path, 'function / statement "die/exit" is blacklisted', 39,
                Issue::LEVEL_CRITICAL),
        ];

        $this->assertEquals($expectedIssues, $issues);
    }


    public function testExit()
    {
        $path = __DIR__ . '/_data/function_blacklist';

        $gadget = new FunctionBlacklistGadget();
        $config = $this->resolve($gadget, ['blacklist' => ['exit' => 'critical']]);

        $issues = $gadget->run($path, $config, new NullLogger())->getIssues();

        $expectedIssues = [
            $this->createIssue($gadget, $path, 'function / statement "die/exit" is blacklisted', 37,
                Issue::LEVEL_CRITICAL),
            $this->createIssue($gadget, $path, 'function / statement "die/exit" is blacklisted', 39,
                Issue::LEVEL_CRITICAL),
        ];

        $this->assertEquals($expectedIssues, $issues);
    }

    public function testNormalFunction()
    {
        $path = __DIR__ . '/_data/function_blacklist';

        $gadget = new FunctionBlacklistGadget();
        $config = $this->resolve($gadget, ['blacklist' => ['extra_var_dump' => 'warning']]);

        $issues = $gadget->run($path, $config, new NullLogger())->getIssues();

        $expectedIssues = [
            $this->createIssue($gadget, $path, 'function / statement "extra_var_dump" is blacklisted', 47,
                Issue::LEVEL_WARNING),
        ];

        $this->assertEquals($expectedIssues, $issues);
    }
}
