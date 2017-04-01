<?php

namespace SimpSpector\Analyser\Console\Command;

use SimpSpector\Analyser\Analyser;
use SimpSpector\Analyser\Formatter\FormatterInterface;
use SimpSpector\Analyser\Logger\ConsoleLogger;
use SimpSpector\Analyser\Repository\RepositoryInterface;
use SimpSpector\Analyser\Util\InitConfigCliHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
     * @param RepositoryInterface $gadgetRepository
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
        $helper = new InitConfigCliHelper($input, $output, $input->getArgument('path'));

        $configFile = $helper->lookupConfigFile();

        $configFileData = [];
        foreach ($this->gadgetRepository->all() as $key => $gadget) {
            $output->writeln("Gadget <info>{$gadget->getDescription()}</info>");

            $binaryFound = $helper->checkBinary($key);

            if (! $helper->askConfirmation("enable?", 1, $defaultAnswer = $binaryFound)) {
                $helper->error('not enabled', 2);
                continue;
            }

            $configFileData[$key] = null;
            $helper->success('enabled', 2);

            $defaultConfigFile = $gadget->getDefaultConfigurationFile();
            if (! $defaultConfigFile) {
                continue;
            }

            if (! $helper->userWantsToWriteConfigFile($defaultConfigFile)) {
                continue;
            }

            $helper->writeGadgetConfigFile($defaultConfigFile);
        }

        $helper->writeConfigFile($configFileData, $configFile);
    }
}
