<?php

namespace SimpSpector\Analyser\Console;

use SimpSpector\Analyser\Console\Command\AnalyseCommand;
use SimpSpector\Analyser\Event\Listener\CleanPathListener;
use SimpSpector\Analyser\Event\Listener\SimpleHighlightListener;
use SimpSpector\Analyser\Event\Subscriber\LoggerSubscriber;
use SimpSpector\Analyser\Events;
use SimpSpector\Analyser\Executor\Executor;
use SimpSpector\Analyser\Formatter\Adapter\DetailAdapter;
use SimpSpector\Analyser\Formatter\Adapter\JsonAdapter;
use SimpSpector\Analyser\Formatter\Adapter\SummaryAdapter;
use SimpSpector\Analyser\Formatter\Formatter;
use SimpSpector\Analyser\Gadget\CommentBlacklistGadget;
use SimpSpector\Analyser\Gadget\FunctionBlacklistGadget;
use SimpSpector\Analyser\Gadget\PhpcsGadget;
use SimpSpector\Analyser\Gadget\PhpmdGadget;
use SimpSpector\Analyser\Gadget\SecurityCheckerGadget;
use SimpSpector\Analyser\Gadget\TwigLintGadget;
use SimpSpector\Analyser\Loader\YamlLoader;
use SimpSpector\Analyser\Repository\Repository;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Application extends BaseApplication
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct('SimpSpector', 'dev');

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(Events::POST_GADGET, [new SimpleHighlightListener(), 'onGadgetResult']);
        $dispatcher->addListener(Events::POST_GADGET, [new CleanPathListener(), 'onGadgetResult'], 100);
        $dispatcher->addSubscriber(new LoggerSubscriber());

        $repository = new Repository();
        $executor   = new Executor($repository, $dispatcher);
        $loader     = new YamlLoader();

        $repository->add(new TwigLintGadget());
        $repository->add(new PhpmdGadget());
        $repository->add(new PhpcsGadget());
        $repository->add(new CommentBlacklistGadget());
        $repository->add(new FunctionBlacklistGadget());
        $repository->add(new SecurityCheckerGadget());

        $formatter = new Formatter();
        $formatter->registerAdapter(new SummaryAdapter());
        $formatter->registerAdapter(new DetailAdapter());
        $formatter->registerAdapter(new JsonAdapter());

        $this->add(new AnalyseCommand($executor, $loader, $formatter));
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'simpspector:analyse';
    }

    /**
     * Overridden so that the application doesn't expect the command
     * name to be the first argument.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        // clear out the normal first argument, which is the command name
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}