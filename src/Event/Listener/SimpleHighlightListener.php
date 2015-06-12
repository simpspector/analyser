<?php

namespace SimpSpector\Analyser\Event\Listener;

use SimpSpector\Analyser\Event\GadgetResultEvent;
use SimpSpector\Analyser\Util\HighlightHelper;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class SimpleHighlightListener
{
    /**
     * @param GadgetResultEvent $event
     */
    public function onGadgetResult(GadgetResultEvent $event)
    {
        $result = $event->getResult();
        $path   = $event->getPath();

        foreach ($result->getIssues() as $issue) {
            if ($issue->getDescription()) {
                continue;
            }

            if (!$issue->getFile() || !$issue->getLine()) {
                continue;
            }

            $issue->setDescription(HighlightHelper::createCodeSnippet($path, $issue, 5, true));
        }
    }
}
