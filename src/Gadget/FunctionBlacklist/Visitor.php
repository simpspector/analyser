<?php

namespace SimpSpector\Analyser\Gadget\FunctionBlacklist;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SimpSpector\Analyser\Gadget\FunctionBlacklistGadget;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Result;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 * @author David Badura <d.a.badura@gmail.com>
 */
class Visitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $currentFile;

    /**
     * @var array
     */
    private $blacklist;

    /**
     * @var Result
     */
    private $result;

    /**
     * @param array $blacklist
     * @param Result $result
     */
    public function __construct(array $blacklist, Result $result)
    {
        $this->blacklist = $blacklist;
        $this->result    = $result;
    }

    /**
     * @param string $file
     */
    public function setCurrentFile($file)
    {
        $this->currentFile = $file;
    }

    /**
     * @param Node $node
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Name && isset($this->blacklist[$node->getFirst()])) {
            $this->addIssueForBlacklistedFunction(
                $node->getFirst(),
                $node,
                $this->translateErrorLevel($this->blacklist[$node->getFirst()])
            );
        }

        if (isset($this->blacklist['die']) && $node instanceof Node\Expr\Exit_) {
            $this->addIssueForBlacklistedFunction(
                'die/exit',
                $node,
                $this->translateErrorLevel($this->blacklist['die'])
            );
        } elseif (isset($this->blacklist['exit']) && $node instanceof Node\Expr\Exit_) {
            $this->addIssueForBlacklistedFunction(
                'die/exit',
                $node,
                $this->translateErrorLevel($this->blacklist['exit'])
            );
        }

        if (isset($this->blacklist['echo']) && $node instanceof Node\Stmt\Echo_) {
            $this->addIssueForBlacklistedFunction(
                'echo',
                $node,
                $this->translateErrorLevel($this->blacklist['echo'])
            );
        }
    }

    /**
     * @return Result
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param \Exception $error
     */
    public function addException(\Exception $error)
    {
        $this->addIssue('Exception: ' . $error->getMessage(), null, Issue::LEVEL_CRITICAL);
    }

    /**
     * @param string $function
     * @param Node $node
     * @param string $level
     */
    private function addIssueForBlacklistedFunction($function, Node $node, $level)
    {
        $this->addIssue(
            sprintf('function / statement "%s" is blacklisted', $function),
            $node,
            $level
        );
    }

    /**
     * @param string $message
     * @param Node $node
     * @param string $level
     */
    private function addIssue($message, Node $node = null, $level = Issue::LEVEL_ERROR)
    {
        $issue = new Issue($message, FunctionBlacklistGadget::NAME, $level);
        $issue->setFile($this->currentFile);

        if ($node) {
            $issue->setLine($node->getLine());
        }

        $this->result->addIssue($issue);
    }

    /**
     * @param string $string
     * @return string
     */
    private function translateErrorLevel($string)
    {
        switch (trim(strtolower($string))) {
            case 'notice':
                return Issue::LEVEL_NOTICE;
            case 'warning':
                return Issue::LEVEL_WARNING;
            case 'error':
                return Issue::LEVEL_ERROR;
            case 'critical':
                return Issue::LEVEL_CRITICAL;
            default:
                throw new \RuntimeException('unknown error level ' . $string);
        }
    }
}
