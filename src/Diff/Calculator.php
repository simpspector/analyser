<?php

namespace SimpSpector\Analyser\Diff;

use SimpSpector\Analyser\Issue;
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
        $fromIssues = $this->createIssuesHashMap($from->getIssues());
        $toIssues = $this->createIssuesHashMap($to->getIssues());

        $result = new Result();

        foreach ($toIssues as $hash => $issue) {
            if (isset($fromIssues[$hash])) {
                unset($fromIssues[$hash]);
                continue;
            }

            $result->newIssues[] = $issue;
        }

        $result->resolvedIssues = array_values($normA);

        return $result;
    }

    /**
     * @param Issue[] $issues
     * @return Issue[]
     */
    private function createIssuesHashMap(array $issues)
    {
        $norm = [];

        foreach ($issues as $issue) {
            $norm[$this->hash($issue)] = $issue;
        }

        return $norm;
    }

    /**
     * @param Issue $issue
     * @return string
     */
    private function hash(Issue $issue)
    {
        return md5($issue->getGadget() . $issue->getFile() . $issue->getLine() . $issue->getTitle());
    }
}