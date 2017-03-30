<?php

namespace SimpSpector\Analyser\Util;

use SimpSpector\Analyser\Struct\ConfigurationFile;
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
        $this->success('assuming project in folder ' . $this->projectPath);

        $configFile = $this->projectPath . '/.simpspector.yml';
        if (! $this->filesystem->exists($configFile)) {
            return $configFile;
        }

        $this->success('found existing .simpspector.yml');
        if (! $this->askConfirmation('Do you want to overwrite it?', 0, $defaultAnswer = false)) {
            throw new \Exception('ending process');
        }

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

        $this->success(".simpspector.yml config file written");
    }

    public function buildConfigFilePath(ConfigurationFile $file)
    {
        return $this->projectPath . '/' . $file->filename;
    }

    public function writeGadgetConfigFile(ConfigurationFile $file)
    {
        $this->filesystem->dumpFile($file->filename, $file->content);

        $this->success("gadget config file written", 2);
    }

    public function askConfirmation($questionText, $indentLevel = 0, $defaultAnswer = true)
    {
        $questionText = str_repeat("\t", $indentLevel) . '<fg=blue>?</> ' . $questionText;
        $helper = new QuestionHelper();
        $question = new ConfirmationQuestion($questionText . ($defaultAnswer ? " (Y/n) " : " (y/N) "), $defaultAnswer);

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
