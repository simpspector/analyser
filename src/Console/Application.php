<?php

namespace SimpSpector\Analyser\Console;

use SimpSpector\Analyser\Analyser;
use SimpSpector\Analyser\AnalyserBuilder;
use SimpSpector\Analyser\Console\Command\AnalyseCommand;
use SimpSpector\Analyser\Formatter\Formatter;
use SimpSpector\Analyser\Formatter\FormatterInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Application extends BaseApplication
{
    /**
     * @param Analyser $analyser
     * @param FormatterInterface $formatter
     * @internal param string $bin
     */
    public function __construct(Analyser $analyser, FormatterInterface $formatter)
    {
        parent::__construct('SimpSpector', 'dev');

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

    /**
     * @param string|null $bin
     * @return self
     */
    public static function create($bin = null)
    {
        $analyser = (new AnalyserBuilder())
            ->setBinaryDir($bin)
            ->build();

        $formatter = Formatter::create();

        return new self($analyser, $formatter);
    }
}
