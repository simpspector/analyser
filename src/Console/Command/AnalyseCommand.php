<?php

namespace SimpSpector\Analyser\Console\Command;

use SimpSpector\Analyser\Executor\ExecutorInterface;
use SimpSpector\Analyser\Formatter\FormatterInterface;
use SimpSpector\Analyser\Loader\LoaderInterface;
use SimpSpector\Analyser\Logger\ConsoleLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author David Badura <d.a.badura@gmail.com>
 */
class AnalyseCommand extends Command
{
    /**
     * @var ExecutorInterface
     */
    private $executor;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * @param ExecutorInterface $executor
     * @param LoaderInterface $loader
     * @param FormatterInterface $formatter
     */
    public function __construct(ExecutorInterface $executor, LoaderInterface $loader, FormatterInterface $formatter)
    {
        parent::__construct();

        $this->executor  = $executor;
        $this->loader    = $loader;
        $this->formatter = $formatter;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('simpspector:analyse')
            ->setDescription('Analyse Project')
            ->addArgument('path', InputArgument::REQUIRED, 'path to the project')
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'path to .simpspector.yml', null)
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, '[json]', 'json');

    }

    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = rtrim($input->getArgument('path'), '/');

        if (!$configFile = $input->getOption('config')) {
            $configFile = $path . '/.simpspector.yml';
        }

        $config = $this->loader->load($configFile);

        $logger = new ConsoleLogger($output);
        $result = $this->executor->run($path, $config, $logger);

        $output->writeln($this->formatter->format($result, $input->getOption('format')));
    }
}