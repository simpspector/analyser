<?php

namespace SimpSpector\Analyser\Util;

use SimpSpector\Analyser\Issue;

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
}