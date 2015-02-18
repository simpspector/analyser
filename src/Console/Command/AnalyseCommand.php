<?php

namespace SimpSpector\Analyser\Console\Command;

use SimpSpector\Analyser\Executor\ExecutorInterface;
use SimpSpector\Analyser\Loader\LoaderInterface;
use SimpSpector\Analyser\Logger\ConsoleLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

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
     * @param ExecutorInterface $executor
     * @param LoaderInterface $loader
     */
    public function __construct(ExecutorInterface $executor, LoaderInterface $loader)
    {
        parent::__construct();

        $this->executor = $executor;
        $this->loader   = $loader;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('simpspector:analyse')
            ->setDescription('Analyse Project')
            ->addArgument('path', InputArgument::REQUIRED, 'path to the project');
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

        $config = $this->loader->load($path . '/simpspector.yml');

        $logger = new ConsoleLogger($output);
        $result = $this->executor->run($path, $config, $logger);

        $serializer = new Serializer([new GetSetMethodNormalizer()], [new JsonEncoder()]);
        $output->write($serializer->serialize($result, 'json', ['json_encode_options' => JSON_PRETTY_PRINT]));
    }
}