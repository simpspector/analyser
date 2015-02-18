<?php

namespace SimpSpector\Analyser\Util;

use SimpSpector\Analyser\Issue;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class HighlightHelper
{
    /**
     * @param string $path
     * @param Issue $issue
     * @param int $around
     * @return string
     */
    public static function createCodeSnippet($path, Issue $issue, $around = 5)
    {
        $snippet = SnippetHelper::createSnippetByFile(
            $path . '/' . $issue->getFile(),
            $issue->getLine(),
            $around
        );

        $extension = pathinfo($issue->getFile(), PATHINFO_EXTENSION);
        $offset    = max($issue->getLine() - $around, 1);

        $options = [
            'file'   => $issue->getFile(),
            'line'   => $issue->getLine(),
            'offset' => $offset
        ];

        return (new MarkdownBuilder())->code($snippet, $extension, $options)->getMarkdown();
    }
}
