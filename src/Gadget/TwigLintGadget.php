<?php

namespace SimpSpector\Analyser\Gadget;

use Asm89\Twig\Lint\StubbedEnvironment;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;
use SimpSpector\Analyser\Util\FilesystemHelper;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class TwigLintGadget extends AbstractGadget
{
    /**
     * @var StubbedEnvironment
     */
    private $twig;

    /**
     *
     */
    public function __construct()
    {
        $this->twig = new StubbedEnvironment(new \Twig_Loader_String());
    }

    /**
     * @param string $path
     * @param array $options
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run($path, array $options, AbstractLogger $logger)
    {
        $options = $this->prepareOptions(
            $options,
            [
                'files'       => ['.'],
                'error_level' => 'error',
            ],
            ['files']
        );

        $result = new Result();
        $files  = FilesystemHelper::findFiles($path, $options['files'], '*.twig');

        foreach ($files as $file) {
            try {
                $this->twig->parse($this->twig->tokenize(file_get_contents($file), $file));
            } catch (\Twig_Error $e) {
                $message = get_class($e) . ': ' . $e->getRawMessage();

                $issue = new Issue($message);
                $issue->setLevel($options['error_level']);
                $issue->setFile($file);
                $issue->setLine($e->getTemplateLine());

                $result->addIssue($issue);
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'twig_lint';
    }
}
