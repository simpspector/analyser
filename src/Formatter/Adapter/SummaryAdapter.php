<?php

namespace SimpSpector\Analyser\Formatter\Adapter;

use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Metric;
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
        $issues  = ResultHelper::sortIssues($result->getIssues());
        $metrics = ResultHelper::sortMetrics($result->getMetrics());

        $markdown = new MarkdownBuilder();

        $markdown->h1(count($issues) . ' Issue(s)');

        $markdown->bulletedList(array_map(function (Issue $issue) {
            return sprintf(
                '%s on line %s: %s',
                $issue->getFile(),
                $issue->getLine(),
                $issue->getTitle()
            );
        }, $issues));

        foreach ($metrics as $key => $metric) {
            if ($metric->getValue() == 0) {
                unset($metrics[$key]);
            }
        }

        $markdown->h1(count($metrics) . ' Metric(s)');

        $markdown->bulletedList(array_map(function (Metric $metric) {
            return sprintf(
                '[%s] %s: %s',
                $metric->getCode(),
                $metric->getTitle(),
                $metric->getValue()
            );
        }, $metrics));

        return $markdown->getMarkdown();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'summary';
    }
}
