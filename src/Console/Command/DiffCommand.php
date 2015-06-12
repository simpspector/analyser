<?php

namespace SimpSpector\Analyser\Console\Command;

use JMS\Serializer\SerializerBuilder;
use SimpSpector\Analyser\Diff\CalculatorInterface;
use SimpSpector\Analyser\Diff\Result as DiffResult;
use SimpSpector\Analyser\Executor\ExecutorInterface;
use SimpSpector\Analyser\Importer\ImporterInterface;
use SimpSpector\Analyser\Loader\LoaderInterface;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Logger\ConsoleLogger;
use SimpSpector\Analyser\Result;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author David Badura <d.a.badura@gmail.com>
 */
class DiffCommand extends Command
{
    /**
     * @var ImporterInterface
     */
    private $importer;

    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @var ExecutorInterface
     */
    private $executor;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @param ImporterInterface $importer
     * @param CalculatorInterface $calculator
     * @param ExecutorInterface $executor
     * @param LoaderInterface $loader
     */
    public function __construct(
        ImporterInterface $importer,
        CalculatorInterface $calculator,
        ExecutorInterface $executor,
        LoaderInterface $loader
    ) {
        parent::__construct();

        $this->importer   = $importer;
        $this->calculator = $calculator;
        $this->executor   = $executor;
        $this->loader     = $loader;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('diff')
            ->setDescription('Diff result')
            ->addArgument('from', InputArgument::REQUIRED, 'path to the project')
            ->addArgument('to', InputArgument::REQUIRED, 'path to the project');
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
        $logger = new ConsoleLogger($output);

        $from = rtrim($input->getArgument('from'), '/');
        $to   = rtrim($input->getArgument('to'), '/');

        $fromResult = $this->getResult($from, $logger);
        $toResult   = $this->getResult($to, $logger);

        $logger->writeln("generate diff");
        $diff = $this->calculator->diff($fromResult, $toResult);

        $output->writeln($this->format($diff));
    }

    /**
     * @param string $path
     * @return Result
     */
    private function getResult($path, AbstractLogger $logger)
    {
        if (is_file($path)) {
            $logger->writeln(sprintf('import file "%s"', $path));

            return $this->importer->import($path);
        }

        $file = $path . '/.simpspector.yml';

        $logger->writeln(sprintf('load config "%s"', $file));
        $config = $this->loader->load($file);

        $logger->writeln('execute gadgets');

        return $this->executor->run($path, $config, $logger);
    }

    /**
     * @param DiffResult $result
     *
     * @return string
     */
    private function format(DiffResult $result)
    {
        $serializer = SerializerBuilder::create()->build();

        return $serializer->serialize($result, 'json');
    }
}
