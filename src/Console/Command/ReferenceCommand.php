<?php

namespace SimpSpector\Analyser\Console\Command;

use SimpSpector\Analyser\Config\ReferenceDumper;
use SimpSpector\Analyser\Repository\RepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author David Badura <d.a.badura@gmail.com>
 */
class ReferenceCommand extends Command
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
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('reference')
            ->setDescription('Dump config reference')
            ->addArgument('gadget', InputArgument::OPTIONAL, 'gadget');
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
        $dumper = new ReferenceDumper($this->repository);
        $output->writeln($dumper->dump($input->getArgument('gadget')));
    }
}