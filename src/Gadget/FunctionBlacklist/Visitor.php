<?php

namespace SimpSpector\Analyser\Gadget\FunctionBlacklist;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SimpSpector\Analyser\Gadget\GadgetInterface;
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
     * @var GadgetInterface
     */
    private $gadget;

    /**
     * @var array
     */
    private $blacklist;

    /**
     * @var Result
     */
    private $result;

    /**
     * @param GadgetInterface $gadget
     * @param array           $blacklist
     * @param Result          $result
     */
    public function __construct(GadgetInterface $gadget, array $blacklist, Result $result)
    {
        $this->gadget = $gadget;
        $this->blacklist = $blacklist;
        $this->result = $result;
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
                $this->blacklist[$node->getFirst()]
            );
        }

        if (isset($this->blacklist['die']) && $node instanceof Node\Expr\Exit_) {
            $this->addIssueForBlacklistedFunction(
                'die/exit',
                $node,
                $this->blacklist['die']
            );
        } elseif (isset($this->blacklist['exit']) && $node instanceof Node\Expr\Exit_) {
            $this->addIssueForBlacklistedFunction(
                'die/exit',
                $node,
                $this->blacklist['exit']
            );
        }

        if (isset($this->blacklist['echo']) && $node instanceof Node\Stmt\Echo_) {
            $this->addIssueForBlacklistedFunction(
                'echo',
                $node,
                $this->blacklist['echo']
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
     * @param Node   $node
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
     * @param Node   $node
     * @param string $level
     */
    private function addIssue($message, Node $node = null, $level = Issue::LEVEL_ERROR)
    {
        $issue = new Issue($this->gadget, $message);
        $issue->setLevel($level);
        $issue->setFile($this->currentFile);

        if ($node) {
            $issue->setLine($node->getLine());
        }

        $this->result->addIssue($issue);
    }
}
