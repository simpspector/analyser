<?php

namespace SimpSpector\Analyser\Diff;

use SimpSpector\Analyser\Issue as AnalyseIssue;
use SimpSpector\Analyser\Metric as AnalyseMetric;
use SimpSpector\Analyser\Result as AnalyseResult;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Calculator implements CalculatorInterface
{
    /**
     * @param AnalyseResult $from
     * @param AnalyseResult $to
     * @return Result
     */
    public function diff(AnalyseResult $from, AnalyseResult $to)
    {
        $result = new Result();

        $this->prepareIssueDiff($from, $to, $result);
        $this->prepareMetricDiff($from, $to, $result);

        return $result;
    }

    /**
     * @param AnalyseResult $from
     * @param AnalyseResult $to
     * @param Result $result
     */
    private function prepareIssueDiff(AnalyseResult $from, AnalyseResult $to, Result $result)
    {
        $fromIssues = $this->createIssuesHashMap($from->getIssues());
        $toIssues   = $this->createIssuesHashMap($to->getIssues());

        foreach ($toIssues as $hash => $issue) {
            if (isset($fromIssues[$hash])) {
                unset($fromIssues[$hash]);
                continue;
            }

            $result->newIssues[] = $issue;
        }

        $result->resolvedIssues = array_values($fromIssues);
    }

    /**
     * @param AnalyseResult $from
     * @param AnalyseResult $to
     * @param Result $result
     */
    private function prepareMetricDiff(AnalyseResult $from, AnalyseResult $to, Result $result)
    {
        $fromMetrics = $this->createMetricsHashMap($from->getMetrics());
        $toMetrics   = $this->createMetricsHashMap($to->getMetrics());

        foreach ($toMetrics as $hash => $toMetric) {
            if (!isset($fromMetrics[$hash])) {
                continue;
            }

            $fromMetric = $fromMetrics[$hash];

            if ($fromMetric->getValue() == $toMetric->getValue()) {
                continue;
            }

            $metric       = new Metric();
            $metric->from = $fromMetric;
            $metric->to   = $toMetric;
            $metric->diff = $toMetric->getValue() - $fromMetric->getValue();

            $result->metricChanges[] = $metric;
        }
    }

    /**
     * @param AnalyseIssue[] $issues
     * @return AnalyseIssue[]
     */
    private function createIssuesHashMap(array $issues)
    {
        $hashMap = [];

        foreach ($issues as $issue) {
            $hashMap[$this->hash($issue)] = $issue;
        }

        return $hashMap;
    }

    /**
     * @param AnalyseMetric[] $metrics
     * @return AnalyseMetric[]
     */
    private function createMetricsHashMap(array $metrics)
    {
        $hashMap = [];

        foreach ($metrics as $metric) {
            $hashMap[$metric->getCode()] = $metric;
        }

        return $hashMap;
    }

    /**
     * @param AnalyseIssue $issue
     * @return string
     */
    private function hash(AnalyseIssue $issue)
    {
        return md5($issue->getGadget() . $issue->getFile() . $issue->getLine() . $issue->getTitle());
    }
}
