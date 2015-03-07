<?php

namespace SimpSpector\Analyser\Console\Command;

use SimpSpector\Analyser\Analyser;
use SimpSpector\Analyser\Formatter\FormatterInterface;
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
     * @var Analyser
     */
    private $analyser;

    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * @param Analyser $analyser
     * @param FormatterInterface $formatter
     */
    public function __construct(Analyser $analyser, FormatterInterface $formatter)
    {
        parent::__construct();

        $this->analyser  = $analyser;
        $this->formatter = $formatter;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('analyse')
            ->setDescription('Analyse Project')
            ->addArgument('path', InputArgument::REQUIRED, 'path to the project')
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'path to .simpspector.yml')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, '[summary|detail|json]', 'summary')
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Output file');

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
        $result = $this->analyser->analyse(
            $input->getArgument('path'),
            $input->getOption('config'),
            new ConsoleLogger($output)
        );

        $output->writeln($this->formatter->format($result, $input->getOption('format')));
        $string = $this->formatter->format($result, $input->getOption('format'));

        if ($file = $input->getOption('output')) {
            file_put_contents($file, $string);
        } else {
            $output->writeln("");
            $output->writeln("");
            $output->writeln($string);
        }
    }
}
