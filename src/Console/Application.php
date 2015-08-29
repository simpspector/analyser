<?php

namespace SimpSpector\Analyser\Console;

use SimpSpector\Analyser\Analyser;
use SimpSpector\Analyser\Console\Command\AnalyseCommand;
use SimpSpector\Analyser\Console\Command\DiffCommand;
use SimpSpector\Analyser\Console\Command\ReferenceCommand;
use SimpSpector\Analyser\DependencyInjection\ContainerConfigurator;
use SimpSpector\Analyser\Diff\Calculator;
use SimpSpector\Analyser\Formatter\FormatterInterface;
use SimpSpector\Analyser\Importer\ImporterInterface;
use SimpSpector\Analyser\Repository\RepositoryInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
    public function __construct(
        Analyser $analyser,
        RepositoryInterface $repository,
        FormatterInterface $formatter,
        ImporterInterface $importer
    ) {
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
        $bin = $bin ? rtrim($bin, '/') . '/' : '';

        $container = new ContainerBuilder();
        $container->setParameter('simpspector.analyser.bin', $bin);
        $container->setParameter('simpspector.analyser.config', realpath(__DIR__ . '/../../config'));

        (new ContainerConfigurator())->prepare($container);

        $container->compile();

        $analyser   = $container->get('simpspector.analyser');
        $repository = $container->get('simpspector.analyser.repository');
        $formatter  = $container->get('simpspector.analyser.formatter');
        $importer   = $container->get('simpspector.analyser.importer');

        return new self($analyser, $repository, $formatter, $importer);
    }
}
