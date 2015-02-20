<?php

namespace SimpSpector\Analyser\Formatter\Adapter;

use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Result;
use SimpSpector\Analyser\Util\MarkdownBuilder;
use SimpSpector\Analyser\Util\ResultHelper;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class DetailAdapter implements AdapterInterface
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

        foreach (ResultHelper::groupIssues($issues) as $file => $issues) {
            $this->renderSection($markdown, $file, $issues);
        }

        return $markdown->getMarkdown();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'detail';
    }

    /**
     * @param MarkdownBuilder $markdown
     * @param string $name
     * @param Issue[] $issues
     */
    protected function renderSection(MarkdownBuilder $markdown, $name, array $issues)
    {
        $markdown->h2($name);

        foreach ($issues as $issue) {
            $this->renderIssue($markdown, $issue);
        }
    }

    /**
     * @param MarkdownBuilder $markdown
     * @param Issue $issue
     */
    protected function renderIssue(MarkdownBuilder $markdown, Issue $issue)
    {
        $header = $issue->getMessage();

        if ($issue->getLine()) {
            $header .= ' on line ' . $issue->getLine();
        }

        $markdown->h3($header);
        $markdown->p($issue->getDescription());
    }
}