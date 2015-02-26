<?php

namespace SimpSpector\Analyser\Event\Subscriber;

use SimpSpector\Analyser\Event\ExecutorResultEvent;
use SimpSpector\Analyser\Event\GadgetResultEvent;
use SimpSpector\Analyser\Events;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Metric;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class CountMetricSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_GADGET  => array('onPostGadget', 0),
            Events::POST_EXECUTE => array('onPostExecute', 0),
        ];
    }

    /**
     * @param GadgetResultEvent $event
     */
    public function onPostGadget(GadgetResultEvent $event)
    {
        $result = $event->getResult();
        $gadget = $event->getGadget();

        $counts = [
            Issue::LEVEL_NOTICE   => 0,
            Issue::LEVEL_WARNING  => 0,
            Issue::LEVEL_ERROR    => 0,
            Issue::LEVEL_CRITICAL => 0
        ];

        foreach ($result->getIssues() as $issue) {
            $counts[$issue->getLevel()]++;
        }

        foreach ($counts as $level => $count) {
            $result->addMetric(
                new Metric(
                    sprintf("count %ss in %s", $level, $gadget->getName()),
                    sprintf("count.%s.%s", $gadget->getName(), $level),
                    $count,
                    Metric::TYPE_COUNT
                )
            );
        }
    }

    /**
     * @param ExecutorResultEvent $event
     */
    public function onPostExecute(ExecutorResultEvent $event)
    {
        $result = $event->getResult();

        $counts = [
            Issue::LEVEL_NOTICE   => 0,
            Issue::LEVEL_WARNING  => 0,
            Issue::LEVEL_ERROR    => 0,
            Issue::LEVEL_CRITICAL => 0
        ];

        foreach ($result->getIssues() as $issue) {
            $counts[$issue->getLevel()]++;
        }

        foreach ($counts as $level => $count) {
            $result->addMetric(
                new Metric(
                    sprintf("count %ss", $level),
                    sprintf("count.%s", $level),
                    $count,
                    Metric::TYPE_COUNT
                )
            );
        }
    }
}