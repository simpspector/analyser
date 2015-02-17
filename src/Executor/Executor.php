<?php

namespace SimpSpector\Analyser\Executor;

use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Logger\NullLogger;
use SimpSpector\Analyser\Repository\RepositoryInterface;
use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Executor implements ExecutorInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $path
     * @param array $config
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run($path, array $config, AbstractLogger $logger = null)
    {
        $logger = $logger ?: new NullLogger();

        $result  = new Result();

        $logger->writeln();
        $logger->writeln("Go go gadgets!");
        $logger->writeln();

        foreach ($config as $type => $options) {

            $gadget = $this->repository->get($type);

            $logger->writeln();
            $logger->writeln("------------------------------------");
            $logger->writeln();

            $logger->writeln(sprintf('run gadget "%s"', $type));
            $logger->writeln();
            $logger->writeln();

            $gadgetResult = $gadget->run($path, $options, $logger);

            $logger->writeln();
            $logger->writeln();
            $logger->writeln(sprintf('%s issues found', count($gadgetResult->getIssues())));

            $result->merge($gadgetResult);
        }

        $logger->writeln();
        $logger->writeln("===============================");
        $logger->writeln();

        $logger->writeln(sprintf('%s issues found', count($result->getIssues())));

        return $result;
    }
} 
