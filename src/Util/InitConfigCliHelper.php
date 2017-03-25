<?php

namespace SimpSpector\Analyser\Util;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Dumper;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class InitConfigCliHelper
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $projectPath;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(InputInterface $input, OutputInterface $output, $projectPath)
    {
        $this->input = $input;
        $this->output = $output;
        $this->projectPath = realpath($projectPath);
        $this->filesystem = new Filesystem();
    }

    /**
     * @return string config file path
     * @throws \Exception
     */
    public function lookupConfigFile()
    {
        $configFile = $this->projectPath . '/.simpspector.yml';

        if ($this->filesystem->exists($configFile)) {
            throw new \Exception('simpspector already configured');
        }

        $this->output->writeln("config file target\n\t<info>$configFile</info>");

        return $configFile;
    }

    public function writeConfigFile(array $data, $file)
    {
        if (empty($data)) {
            return;
        }

        $yamlDumper = new Dumper();
        $yamlData = $yamlDumper->dump(["gadgets" => $data], $inline = 4);
        $this->filesystem->dumpFile($file, $yamlData);

        $this->output->writeln("<info>success</info> config file written");
    }

    public function askConfirmation($questionText, $indentLevel = 0)
    {
        $questionText = str_repeat("\t", $indentLevel) . $questionText;
        $helper = new QuestionHelper();
        $question = new ConfirmationQuestion("$questionText (Y/n) ");

        return $helper->ask($this->input, $this->output, $question);
    }

    public function checkBinary($binary)
    {
        $found = `which $binary`;

        if ($found) {
            $this->success("$binary binary found", 1);
            return true;
        }

        $this->error("$binary binary not found", 1);
    }

    public function success($message, $indentLevel = 0)
    {
        $prefix = str_repeat("\t", $indentLevel);
        $this->output->writeln("$prefix<fg=green>✔</> $message");
    }

    public function error($message, $indentLevel = 0)
    {
        $prefix = str_repeat("\t", $indentLevel);
        $this->output->writeln("$prefix<fg=red>✗</> $message");
    }
}
