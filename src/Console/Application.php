<?php

namespace SimpSpector\Analyser\Console;

use SimpSpector\Analyser\Analyser;
use SimpSpector\Analyser\AnalyserBuilder;
use SimpSpector\Analyser\Console\Command\AnalyseCommand;
use SimpSpector\Analyser\Console\Command\ReferenceCommand;
use SimpSpector\Analyser\Console\Command\DiffCommand;
use SimpSpector\Analyser\Diff\Calculator;
use SimpSpector\Analyser\Formatter\Formatter;
use SimpSpector\Analyser\Formatter\FormatterInterface;
use SimpSpector\Analyser\Importer\Importer;
use SimpSpector\Analyser\Importer\ImporterInterface;
use SimpSpector\Analyser\Repository\RepositoryInterface;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Application extends BaseApplication
{
    /**
     * @param Analyser $analyser
     * @param RepositoryInterface $repository
     * @param FormatterInterface $formatter
     * @param ImporterInterface $importer
     */
    public function __construct(Analyser $analyser, RepositoryInterface $repository, FormatterInterface $formatter, ImporterInterface $importer)
    {
        parent::__construct('SimpSpector', 'dev');

        $this->add(new AnalyseCommand($analyser, $formatter));
        $this->add(new ReferenceCommand($repository));
        $this->add(new DiffCommand($importer, new Calculator(), $analyser->getExecutor(), $analyser->getLoader()));
    }

    /**
     * @param string|null $bin
     * @return self
     */
    public static function create($bin = null)
    {
        $buider = (new AnalyserBuilder())
            ->setBinaryDir($bin);

        $analyser   = $buider->build();
        $repository = $buider->getRepository();

        $formatter = Formatter::create();
        $importer  = Importer::create();

        return new self($analyser, $repository, $formatter, $importer);
    }
}
