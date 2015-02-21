<?php

namespace SimpSpector\Analyser\Executor;

use SimpSpector\Analyser\Event\ExecutorEvent;
use SimpSpector\Analyser\Event\ExecutorResultEvent;
use SimpSpector\Analyser\Event\GadgetEvent;
use SimpSpector\Analyser\Event\GadgetResultEvent;
use SimpSpector\Analyser\Events;
use SimpSpector\Analyser\Gadget\GadgetInterface;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Logger\NullLogger;
use SimpSpector\Analyser\Repository\RepositoryInterface;
use SimpSpector\Analyser\Result;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Executor implements ExecutorInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param RepositoryInterface $repository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(RepositoryInterface $repository, EventDispatcherInterface $eventDispatcher = null)
    {
        $this->repository      = $repository;
        $this->eventDispatcher = $eventDispatcher ?: new EventDispatcher();
    }

    /**
     * @param string $path
     * @param array $config
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run($path, array $config, AbstractLogger $logger = null)
    {
        $path   = $this->preparePath($path);
        $logger = $logger ?: new NullLogger();

        $event = new ExecutorEvent($path, $config, $logger);
        $this->eventDispatcher->dispatch(Events::PRE_EXECUTE, $event);
        $config = $event->getConfig();

        $result = $this->executeGadgets($path, $config, $logger);

        $event = new ExecutorResultEvent($path, $config, $result, $logger);
        $this->eventDispatcher->dispatch(Events::POST_EXECUTE, $event);

        return $result;
    }

    /**
     * @param string $path
     * @param array $config
     * @param AbstractLogger $logger
     * @return Result
     */
    private function executeGadgets($path, array $config, AbstractLogger $logger)
    {
        $result = new Result();
        foreach ($config as $type => $options) {
            $gadget       = $this->repository->get($type);
            $gadgetResult = $this->executeGadget($gadget, $path, $options, $logger);

            $result->merge($gadgetResult);
        }

        return $result;
    }

    /**
     * @param GadgetInterface $gadget
     * @param $path
     * @param array $options
     * @param AbstractLogger $logger
     * @return Result
     */
    private function executeGadget(GadgetInterface $gadget, $path, array $options, AbstractLogger $logger)
    {
        $event = new GadgetEvent($path, $options, $gadget, $logger);
        $this->eventDispatcher->dispatch(Events::PRE_GADGET, $event);
        $options = $event->getOptions();

        $result = $gadget->run($path, $options, $logger);
        $this->finishGadgetResult($gadget, $result);

        $event = new GadgetResultEvent($path, $options, $gadget, $result, $logger);
        $this->eventDispatcher->dispatch(Events::POST_GADGET, $event);

        return $result;
    }

    /**
     * @param $path
     * @return string
     */
    private function preparePath($path)
    {
        return realpath($path);
    }

    /**
     * @param GadgetInterface $gadget
     * @param Result $result
     */
    private function finishGadgetResult(GadgetInterface $gadget, Result $result)
    {
        foreach ($result->getIssues() as $issue) {
            $issue->setGadget($gadget->getName());
        }
    }
} 
