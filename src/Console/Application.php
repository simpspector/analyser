<?php

namespace SimpSpector\Analyser\Console;

use SimpSpector\Analyser\AnalyserBuilder;
use SimpSpector\Analyser\Console\Command\AnalyseCommand;
use SimpSpector\Analyser\Formatter\Formatter;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Application extends BaseApplication
{
    /**
     * @param string $bin
     */
    public function __construct($bin = '')
    {
        parent::__construct('SimpSpector', 'dev');

        if ($bin) {
            $bin = rtrim($bin, '/') . '/';
        }

        $analyser = (new AnalyserBuilder())
            ->setBinaryPath($bin)
            ->build();

        $formatter = Formatter::create();

        $this->add(new AnalyseCommand($analyser, $formatter));
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
