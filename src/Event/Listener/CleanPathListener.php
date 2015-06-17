<?php

namespace SimpSpector\Analyser\Event\Listener;

use SimpSpector\Analyser\Event\GadgetResultEvent;
use Webmozart\PathUtil\Path;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class CleanPathListener
{
    /**
     * @param GadgetResultEvent $event
     */
    public function onGadgetResult(GadgetResultEvent $event)
    {
        $result = $event->getResult();
        $path   = $event->getPath();

        foreach ($result->getIssues() as $issue) {
            if (!$issue->getFile()) {
                continue;
            }

            $issue->setFile(Path::makeRelative($issue->getFile(), $path));
        }
    }
}
