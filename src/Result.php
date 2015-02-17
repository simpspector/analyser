<?php

namespace SimpSpector\Analyser;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Result
{
    /**
     * @var Issue[]
     */
    private $issues;

    /**
     * @param array $issues
     */
    public function __construct(array $issues = [])
    {
        $this->issues = $issues;
    }

    /**
     * @param Issue $issue
     */
    public function addIssue(Issue $issue)
    {
        $this->issues[] = $issue;
    }

    /**
     * @return Issue[]
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * @param Result $result
     */
    public function merge(Result $result)
    {
        $this->issues = array_merge($this->issues, $result->getIssues());
    }
}