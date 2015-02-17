<?php

namespace SimpSpector\Analyser\Tests\Gadget;

use SimpSpector\Analyser\Gadget\CommentBlacklistGadget;
use SimpSpector\Analyser\Logger\NullLogger;

/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
class CommentBlacklistGadgetTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommentBlacklistGadget */
    private $OUT;

    protected function setUp()
    {
        $this->OUT = new CommentBlacklistGadget();
    }

    public function testFixtures()
    {
        $path   = __DIR__ . '/_data/comment_blacklist';
        $config = [];

        $issues = $this->OUT->run($path, $config, new NullLogger())->getIssues();

        $this->assertEquals(5, count($issues));
        $lineNumbers = [];
        foreach ($issues as $issue) {
            $this->assertStringEndsWith('foo.php', $issue->getFile());
            $lineNumbers[] = $issue->getLine();
        }
        $this->assertEquals([11, 19, 21, 23, 27], $lineNumbers);
    }
} 
