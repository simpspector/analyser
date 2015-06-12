<?php

namespace SimpSpector\Analyser\Event\Subscriber;

use SimpSpector\Analyser\Event\ExecutorEvent;
use SimpSpector\Analyser\Event\ExecutorResultEvent;
use SimpSpector\Analyser\Event\GadgetEvent;
use SimpSpector\Analyser\Event\GadgetResultEvent;
use SimpSpector\Analyser\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::PRE_EXECUTE  => array('onPreExecute', 0),
            Events::PRE_GADGET   => array('onPreGadget', 0),
            Events::POST_GADGET  => array('onPostGadget', 0),
            Events::POST_EXECUTE => array('onPostExecute', 0),
        ];
    }

    /**
     * @param ExecutorEvent $event
     */
    public function onPreExecute(ExecutorEvent $event)
    {
        $logger = $event->getLogger();

        $logger->writeln();
        $logger->writeln("Go go gadgets!");
        $logger->writeln();
    }

    /**
     * @param GadgetEvent $event
     */
    public function onPreGadget(GadgetEvent $event)
    {
        $logger = $event->getLogger();
        $gadget = $event->getGadget();

        $logger->writeln();
        $logger->writeln("------------------------------------");
        $logger->writeln();

        $logger->writeln(sprintf('run gadget "%s"', $gadget->getName()));
        $logger->writeln();
        $logger->writeln();
    }

    /**
     * @param GadgetResultEvent $event
     */
    public function onPostGadget(GadgetResultEvent $event)
    {
        $logger = $event->getLogger();
        $result = $event->getResult();

        $logger->writeln();
        $logger->writeln();
        $logger->writeln(sprintf('%s issues found', count($result->getIssues())));
    }

    /**
     * @param ExecutorResultEvent $event
     */
    public function onPostExecute(ExecutorResultEvent $event)
    {
        $logger = $event->getLogger();
        $result = $event->getResult();

        $logger->writeln();
        $logger->writeln("===============================");
        $logger->writeln();
        $logger->writeln(sprintf('%s issues found', count($result->getIssues())));
    }
}
