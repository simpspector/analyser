<?php

namespace Gadget;

use SimpSpector\Analyser\Gadget\DocBlockGadget;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Logger\NullLogger;
use SimpSpector\Analyser\Tests\Gadget\GadgetTestCase;

class DocBlockGadgetTest extends GadgetTestCase
{
    /**
     * @var DocBlockGadget
     */
    private $OUT;

    protected function setUp()
    {
        $this->OUT = new DocBlockGadget();
    }

    public function testDefaultConfig()
    {
        $this->assertConfig($this->OUT, [
            'files'                         => ['./'],
            'missing_docblock'              => 'error',
            'missing_newline_before_return' => 'notice',
            'obsolete_variable'             => 'notice',
            'missing_variable'              => 'notice',
            'type_missmatch'                => 'notice',
        ], []);
    }

    public function testDefaultValue()
    {
        $path = __DIR__ . '/_data/doc_block/DefaultValue';

        $issues = $this
            ->OUT
            ->run($path, $this->resolve($this->OUT, []), new NullLogger())
            ->getIssues();

        $this->assertEquals([], $issues);
    }

    public function testPrimitiveDatatypehint()
    {
        $path = __DIR__ . '/_data/doc_block/PrimitiveDatatypehint';

        $issues = $this
            ->OUT
            ->run($path, $this->resolve($this->OUT, []), new NullLogger())
            ->getIssues();

        $this->assertEquals([], $issues);
    }

    public function testArrayAndArray()
    {
        $path = __DIR__ . '/_data/doc_block/ArrayAndArray';

        $issues = $this
            ->OUT
            ->run($path, $this->resolve($this->OUT, []), new NullLogger())
            ->getIssues();

        $this->assertEquals([], $issues);
    }

    public function testDetectMissingDocBlock()
    {
        $path = __DIR__ . '/_data/doc_block/MissingDocBlock';

        $issues = $this
            ->OUT
            ->run($path, $this->resolve($this->OUT, []), new NullLogger())
            ->getIssues();

        $this->assertEquals([
            $this->createIssue($path, '"Foo::__construct()" missing docblock', 5, Issue::LEVEL_ERROR)
        ], $issues);
    }

    public function testDoNotEnforceUnnecessaryDocBlocks()
    {
        $path = __DIR__ . '/_data/doc_block/MissingDocBlock';

        $issues = $this
            ->OUT
            ->run($path, $this->resolve($this->OUT, ['missing_docblock' => null]), new NullLogger())
            ->getIssues();

        $this->assertEquals([], $issues);
    }

    public function testDetectMissingVariableInDocBlock()
    {
        $path = __DIR__ . '/_data/doc_block/MissingVariableInDocBlock';

        $issues = $this
            ->OUT
            ->run($path, $this->resolve($this->OUT, []), new NullLogger())
            ->getIssues();

        $this->assertEquals(
            [
                $this->createIssue(
                    $path,
                    '"Foo::bar($baz, $faz)" missing variable $faz in docblock',
                    10,
                    Issue::LEVEL_NOTICE)
            ],
            $issues
        );
    }

    public function testDetectObsoleteVariableInDocBlock()
    {
        $path = __DIR__ . '/_data/doc_block/ObsoleteVariableInDocBlock';

        $issues = $this->OUT
            ->run($path, $this->resolve($this->OUT, []), new NullLogger())
            ->getIssues();

        $this->assertEquals([
            $this->createIssue($path, '"Foo::bar($baz)" obsolete variable $faz in docblock', 11, Issue::LEVEL_NOTICE)
        ], $issues);
    }

    public function testDetectTypeMismatch()
    {
        $path = __DIR__ . '/_data/doc_block/TypeMissmatch';

        $issues = $this->OUT
            ->run($path, $this->resolve($this->OUT, []), new NullLogger())
            ->getIssues();

        $this->assertEquals(
            [
                $this->createIssue(
                    $path,
                    '"Foo::bar(Foo $baz)" variable $baz has type \object in docblock',
                    10,
                    Issue::LEVEL_NOTICE
                )
            ],
            $issues
        );
    }

    public function testMultipleTypesInDocblockMatch()
    {
        $path = __DIR__ . '/_data/doc_block/MultipleTypesInDocblock';

        $issues = $this->OUT
            ->run($path, $this->resolve($this->OUT, []), new NullLogger())
            ->getIssues();

        $this->assertEquals([], $issues);
    }

    public function testNullIsPrimitive()
    {
        $path = __DIR__ . '/_data/doc_block/NullIsPrimitive';

        $issues = $this->OUT
            ->run($path, $this->resolve($this->OUT, []), new NullLogger())
            ->getIssues();

        $this->assertEquals([], $issues);
    }

    public function testAllowSpaceAfterCommaInParameterList()
    {
        $path = __DIR__ . '/_data/doc_block/AllowSpaceAfterCommaInParameterList';

        $issues = $this->OUT
            ->run($path, $this->resolve($this->OUT, []), new NullLogger())
            ->getIssues();

        $this->assertEquals([], $issues);
    }

    public function testAnonymousFunctionsDontNeedDocblocks()
    {
        $path = __DIR__ . '/_data/doc_block/AnonymousFunctions';

        $issues = $this->OUT
            ->run($path, $this->resolve($this->OUT, []), new NullLogger())
            ->getIssues();

        $this->assertEquals([], $issues);
    }

    public function testGlobalNamespaceType()
    {
        $path = __DIR__ . '/_data/doc_block/GlobalNamespaceType';

        $issues = $this->OUT
            ->run($path, $this->resolve($this->OUT, []), new NullLogger())
            ->getIssues();

        $this->assertEquals([], $issues);
    }

    public function testEnforceNewlineBeforeReturn()
    {
        // @todo
    }

    public function testDetectWrongReturnType()
    {
        // @todo
    }

    public function testFunctionDocBlockInGlobalNamespace()
    {
        // @todo
    }

    /**
     * @param string $path
     * @param string $message
     * @param int $line
     * @param string $level
     *
     * @return Issue
     */
    private function createIssue($path, $message, $line, $level)
    {
        $issue = new Issue($this->OUT, $message);
        $issue->setLevel($level);
        $issue->setFile($path . '/Foo.php');
        $issue->setLine($line);

        return $issue;
    }

}
