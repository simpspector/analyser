<?php

namespace SimpSpector\Analyser\Formatter\Adapter;

use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Result;
use SimpSpector\Analyser\Util\MarkdownBuilder;
use SimpSpector\Analyser\Util\ResultHelper;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class SummaryAdapter implements AdapterInterface
{
    /**
     * @param Result $result
     * @return string
     */
    public function format(Result $result)
    {
        $issues = ResultHelper::sortIssues($result->getIssues());

        $markdown = new MarkdownBuilder();

        $markdown->h1(count($issues) . ' Issues');

        $markdown->bulletedList(array_map(function (Issue $issue) {
            return sprintf(
                '%s on line %s: %s',
                $issue->getFile(),
                $issue->getLine(),
                $issue->getMessage()
            );
        }, $issues));

        return $markdown->getMarkdown();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'summary';
    }

    /**
     * @param Issue[] $issues
     * @return Issue[]
     */
    protected function sortIssues(array $issues)
    {
        usort($issues, function (Issue $a, Issue $b) {
            if (0 !== $cmp = strcmp($a->getFile(), $b->getFile())) {
                return $cmp;
            }

            return $b->getLine() - $a->getLine();
        });

        return $issues;
    }
}