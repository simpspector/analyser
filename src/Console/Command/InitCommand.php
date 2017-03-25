<?php

namespace SimpSpector\Analyser\Console\Command;

use SimpSpector\Analyser\Analyser;
use SimpSpector\Analyser\Formatter\FormatterInterface;
use SimpSpector\Analyser\Logger\ConsoleLogger;
use SimpSpector\Analyser\Repository\RepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Filesystem\Filesystem;

/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class InitCommand extends Command
{
    /**
     * @var RepositoryInterface
     */
    private $gadgetRepository;

    /**
     * @param Analyser $analyser
     */
    public function __construct(RepositoryInterface $gadgetRepository)
    {
        parent::__construct();

        $this->gadgetRepository = $gadgetRepository;
    }

    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('setup configuration for new project')
            ->addArgument('path', InputArgument::OPTIONAL, 'path to the project', '.');
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
        $filesystem = new Filesystem();
        $yamlDumper = new Dumper();
        $gadgets = $this->gadgetRepository->all();
        $questionHelper = $this->getHelper('question');

        $folder = realpath($input->getArgument('path'));
        $configFile = "$folder/.simpspector.yml";
        $configFileData = [];

        if ($filesystem->exists($configFile)) {
            throw new \Exception('simpspector already configured');
        }

        $output->writeln("config file target\n\t<info>$configFile</info>");

        foreach ($gadgets as $key => $gadget) {
            $output->writeln("Gadget <info>{$gadget->getDescription()}</info>");

            $binaryFound = `which $key`;
            if ($binaryFound) {
                $output->writeln("\t<info>✔</info> $key binary found");
            } else {
                $output->writeln("\t<fg=red>✗</> $key binary not found");
            }

            $question = new ConfirmationQuestion("\tenable? (Y/n) ");

            if ($questionHelper->ask($input, $output, $question)) {
                $configFileData[$key] = null;
            }
        }

        if (! empty($configFileData)) {
            $yamlData = $yamlDumper->dump(["gadgets" => $configFileData], $inline = 4);
            $filesystem->dumpFile($configFile, $yamlData);
            $output->writeln("<info>success</info> config file written");
        }
    }
}
