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
     * @var Metric[]
     */
    private $metrics;

    /**
     * @param Issue[] $issues
     * @param Metric[] $metrics
     */
    public function __construct(array $issues = [], array $metrics = [])
    {
        $this->issues  = $issues;
        $this->metrics = $metrics;
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
     * @param Metric $metric
     */
    public function addMetric(Metric $metric)
    {
        $this->metrics[] = $metric;
    }

    /**
     * @return Metric[]
     */
    public function getMetrics()
    {
        return $this->metrics;
    }

    /**
     * @param Result $result
     */
    public function merge(Result $result)
    {
        $this->issues  = array_merge($this->issues, $result->getIssues());
        $this->metrics = array_merge($this->metrics, $result->getMetrics());
    }
}