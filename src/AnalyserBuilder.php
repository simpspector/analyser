<?php

namespace SimpSpector\Analyser;

use SimpSpector\Analyser\Event\Listener\CleanPathListener;
use SimpSpector\Analyser\Event\Listener\SimpleHighlightListener;
use SimpSpector\Analyser\Event\Subscriber\LoggerSubscriber;
use SimpSpector\Analyser\Event\Subscriber\MetricsCollectorSubscriber;
use SimpSpector\Analyser\Executor\Executor;
use SimpSpector\Analyser\Gadget;
use SimpSpector\Analyser\Gadget\GadgetInterface;
use SimpSpector\Analyser\Loader\LoaderInterface;
use SimpSpector\Analyser\Loader\YamlLoader;
use SimpSpector\Analyser\Repository\Repository;
use SimpSpector\Analyser\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class AnalyserBuilder
{
    /**
     * @var bool
     */
    protected $enableDefaultListener = true;

    /**
     * @var bool
     */
    protected $enableDefaultGadgets = true;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var GadgetInterface[]
     */
    protected $gadgets = [];

    /**
     * @var string
     */
    protected $binDir;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     * @param RepositoryInterface $repository
     * @return $this
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableDefaultListener()
    {
        $this->enableDefaultListener = false;

        return $this;
    }

    /**
     * @param GadgetInterface $gadget
     * @return $this
     */
    public function addGadget(GadgetInterface $gadget)
    {
        $this->gadgets[] = $gadget;

        return $this;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function setBinaryDir($dir)
    {
        $this->binDir = rtrim($dir, '/');

        return $this;
    }

    /**
     * @return Analyser
     */
    public function build()
    {
        $dispatcher = $this->dispatcher ?: new EventDispatcher();
        $repository = $this->repository ?: new Repository();
        $loader     = $this->loader ?: new YamlLoader();

        if ($this->enableDefaultListener) {
            $this->registerDefaultListener($dispatcher);
        }

        if ($this->enableDefaultGadgets) {
            $this->registerDefaultGadgets($repository);
        }

        foreach ($this->gadgets as $gadget) {
            $repository->add($gadget);
        }

        $executor = new Executor($repository, $dispatcher);

        return new Analyser($executor, $loader);
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    protected function registerDefaultListener(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addListener(Events::POST_GADGET, [new SimpleHighlightListener(), 'onGadgetResult']);
        $dispatcher->addListener(Events::POST_GADGET, [new CleanPathListener(), 'onGadgetResult'], 100);
        $dispatcher->addSubscriber(new LoggerSubscriber());
        $dispatcher->addSubscriber(new MetricsCollectorSubscriber());
    }

    /**
     * @param RepositoryInterface $repository
     */
    protected function registerDefaultGadgets(RepositoryInterface $repository)
    {
        $binDir = $this->binDir ? $this->binDir . '/' : '';

        $repository->add(new Gadget\TwigLintGadget());
        $repository->add(new Gadget\PhpmdGadget($binDir . 'phpmd'));
        $repository->add(new Gadget\PhpcsGadget($binDir . 'phpcs'));
        $repository->add(new Gadget\CommentBlacklistGadget());
        $repository->add(new Gadget\FunctionBlacklistGadget());
        $repository->add(new Gadget\SecurityCheckerGadget($binDir . 'security-checker'));
    }
}