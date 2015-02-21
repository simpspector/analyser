<?php

namespace SimpSpector\Analyser\Gadget;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use SimpSpector\Analyser\Gadget\FunctionBlacklist\Visitor;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;
use SimpSpector\Analyser\Util\FilesystemHelper;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class FunctionBlacklistGadget extends AbstractGadget
{
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
                'files'     => ['.'],
                'blacklist' => [
                    'die'      => 'error',
                    'var_dump' => 'error',
                    'echo'     => 'warning',
                    'dump'     => 'error',
                ],
            ],
            ['files', 'blacklist']
        );

        $result    = new Result();
        $parser    = new Parser(new Lexer());
        $visitor   = new Visitor($options['blacklist'], $result);
        $traverser = new NodeTraverser();

        $traverser->addVisitor($visitor);

        $files = FilesystemHelper::findFiles($path, $options['files']);

        foreach ($files as $file) {
            try {
                $visitor->setCurrentFile($file);
                $statements = $parser->parse(file_get_contents($file));
                $traverser->traverse($statements);
            } catch (\Exception $e) {
                $visitor->addException($e);
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'function_blacklist';
    }
}
