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
use Webmozart\PathUtil\Path;

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
        $this->repository = $repository;
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
        $path = Path::canonicalize($path);
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
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
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
        foreach ($config['gadgets'] as $type => $arguments) {
            $gadget = $this->repository->get($type);
            $gadgetResult = $this->executeGadget($gadget, $path, (array)$arguments, $logger);

            $result->merge($gadgetResult);
        }

        return $result;
    }

    /**
     * @param GadgetInterface $gadget
     * @param string $path
     * @param array $arguments
     * @param AbstractLogger $logger
     * @return Result
     */
    private function executeGadget(GadgetInterface $gadget, $path, array $arguments, AbstractLogger $logger)
    {
        $event = new GadgetEvent($path, $arguments, $gadget, $logger);
        $this->eventDispatcher->dispatch(Events::PRE_GADGET, $event);
        $arguments = $event->getArguments();

        $result = $gadget->run($path, $arguments, $logger);

        $event = new GadgetResultEvent($path, $arguments, $gadget, $result, $logger);
        $this->eventDispatcher->dispatch(Events::POST_GADGET, $event);

        return $result;
    }
}
