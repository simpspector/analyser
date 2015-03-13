<?php

namespace SimpSpector\Analyser\Gadget;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use SimpSpector\Analyser\Gadget\FunctionBlacklist\Visitor;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;
use SimpSpector\Analyser\Util\FilesystemHelper;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class FunctionBlacklistGadget implements GadgetInterface
{
    /**
     * @param ArrayNodeDefinition $node
     */
    public function configure(ArrayNodeDefinition $node)
    {
        $node->children()
            ->node('files', 'paths')->defaultValue(['./'])->end()
            ->node('blacklist', 'level_map')->defaultValue([
                'die'      => Issue::LEVEL_ERROR,
                'var_dump' => Issue::LEVEL_ERROR,
                'echo'     => Issue::LEVEL_WARNING,
                'dump'     => Issue::LEVEL_ERROR,
            ])->end()
        ->end();
    }

    /**
     * @param string $path
     * @param array $options
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run($path, array $options, AbstractLogger $logger)
    {
        $result    = new Result();
        $parser    = new Parser(new Lexer());
        $visitor   = new Visitor($this, $options['blacklist'], $result);
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
