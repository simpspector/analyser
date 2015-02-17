<?php

namespace SimpSpector\Analyser\Console;

use SimpSpector\Analyser\Console\Command\AnalyseCommand;
use SimpSpector\Analyser\Executor\Executor;
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

        $repository = new Repository();
        $executor   = new Executor($repository);
        $loader     = new YamlLoader();

        $repository->add(new TwigLintGadget());
        $repository->add(new PhpmdGadget());
        $repository->add(new PhpcsGadget());
        $repository->add(new CommentBlacklistGadget());
        $repository->add(new FunctionBlacklistGadget());
        $repository->add(new SecurityCheckerGadget());

        $this->add(new AnalyseCommand($executor, $loader));
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