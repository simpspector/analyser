<?php

namespace SimpSpector\Analyser\Util;

use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Metric;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class ResultHelper
{
    /**
     * @param Issue[] $issues
     * @return Issue[]
     */
    public static function sortIssues(array $issues)
    {
        usort($issues, function (Issue $a, Issue $b) {
            if (0 !== $cmp = strcmp($a->getFile(), $b->getFile())) {
                return $cmp;
            }

            return $b->getLine() - $a->getLine();
        });

        return $issues;
    }

    /**
     * @param Issue[] $issues
     * @return array
     */
    public static function groupIssues(array $issues)
    {
        $result = [];

        foreach ($issues as $issue) {
            $result[$issue->getFile()][] = $issue;
        }

        return $result;
    }

    /**
     * @param Metric[] $metrics
     * @return Metric[]
     */
    public static function sortMetrics(array $metrics)
    {
        usort($metrics, function (Metric $a, Metric $b) {
            // sort root or not so deep metrics to the top
            if (0 != $diff = self::codeDepth($a) - self::codeDepth($b)) {
                return $diff;
            }

            return strcmp($a->getCode(), $b->getCode());
        });

        return $metrics;
    }

    /**
     * @param Metric $metric
     * @return int
     */
    private static function codeDepth(Metric $metric)
    {
        return substr_count($metric->getCode(), '.');
    }
}