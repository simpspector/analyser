<?php

namespace SimpSpector\Analyser\Gadget;

use SimpSpector\Analyser\Gadget\DocBlockGadget\Differ;
use SimpSpector\Analyser\Gadget\DocBlockGadget\IssueFactory;
use SimpSpector\Analyser\Gadget\DocBlockGadget\FunctionParameter;
use SimpSpector\Analyser\Gadget\DocBlockGadget\FunctionSignature;
use phpDocumentor\Reflection\DocBlock;
use SimpSpector\Analyser\Issue;
use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Result;
use SimpSpector\Analyser\Util\FilesystemHelper;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class DocBlockGadget implements GadgetInterface
{
    /**
     * @var Differ
     */
    private $differ;

    /**
     * @var IssueFactory
     */
    private $issueFactory;

    /**
     * @param Differ $differ
     */
    public function __construct(Differ $differ)
    {
        $this->differ       = $differ;
        $this->issueFactory = new IssueFactory($this);
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    public function configure(ArrayNodeDefinition $node)
    {
        $node->children()
             ->node('files', 'paths')->defaultValue(['./'])->end()
             ->node('missing_docblock', 'nullable_level')->defaultValue(Issue::LEVEL_ERROR)->end()
             ->node('missing_newline_before_return', 'nullable_level')->defaultValue(Issue::LEVEL_NOTICE)->end()
             ->node('obsolete_variable', 'nullable_level')->defaultValue(Issue::LEVEL_NOTICE)->end()
             ->node('missing_variable', 'nullable_level')->defaultValue(Issue::LEVEL_NOTICE)->end()
             ->node('missing_type_in_docblock', 'nullable_level')->defaultValue(Issue::LEVEL_NOTICE)->end()
             ->node('missing_type_in_signature', 'nullable_level')->defaultValue(Issue::LEVEL_NOTICE)->end()
             ->node('type_missmatch', 'nullable_level')->defaultValue(Issue::LEVEL_NOTICE)->end()
         ->end();
    }

    /**
     * @param string $path
     * @param array $options
     * @param AbstractLogger $logger
     *
     * @return Result
     */
    public function run($path, array $options, AbstractLogger $logger)
    {
        $result = new Result();
        $files  = FilesystemHelper::findFiles($path, $options['files']);
        foreach ($files as $file) {
            foreach ((new \PHP_Token_Stream($file))->getClasses() as $className => $class) {
                foreach ($class['methods'] as $methodName => $method) {
                    if ($methodName === 'anonymous function') {
                        continue;
                    }
                    $result->merge($this->processMethod($options, $method, $className, $file));
                }
            }
        }

        return $result;

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'doc_block';
    }

    /**
     * @param array $options
     * @param array $method
     * @param string $className
     * @param string $file
     *
     * @return Result
     */
    private function processMethod(array $options, array $method, $className, $file)
    {
        $result = new Result();
        if ($method['docblock'] === null) {
            if ($options['missing_docblock'] !== null) {
                $result->addIssue(
                    $this->issueFactory->createIssueForMissingDocblock(
                        $options,
                        $method,
                        $className,
                        $file
                    )
                );
            }

            return $result;
        }

        $docBlock = new DocBlock($method['docblock']);

        $tagsInDocblock = $docBlock->getTags();
        $signature      = new FunctionSignature($method['signature']);

        if ($options['type_missmatch'] !== null) {
            foreach ($this->differ->inDocBlockAndSignature($tagsInDocblock, $signature) as $params) {
                /** @var DocBlock\Tag\ParamTag $docBlockParam */
                $docBlockParam = $params[0];
                /** @var FunctionParameter $functionParameter */
                $functionParameter = $params[1];

                if (! $this->differ->equalTypes($functionParameter, $docBlockParam)) {
                    if (! $functionParameter->type) {
                        $result->addIssue(
                            $this->issueFactory->createIssueForMissingTypeInSignature(
                                $options,
                                $method,
                                $className,
                                $file,
                                $functionParameter
                            )
                        );
                    } elseif (! $docBlockParam->getType()) {
                        $result->addIssue(
                            $this->issueFactory->createIssueForMissingTypeInDocBlock(
                                $options,
                                $method,
                                $className,
                                $file,
                                $docBlockParam
                            )
                        );
                    } else {
                        $result->addIssue(
                            $this->issueFactory->createIssueForMismatchingTypes(
                                $options,
                                $method,
                                $className,
                                $file,
                                $functionParameter,
                                $docBlockParam
                            )
                        );
                    }
                }
            }
        }
        if ($options['obsolete_variable'] !== null) {
            foreach ($this->differ->inDocblockOnly($tagsInDocblock, $signature) as $param) {
                $result->addIssue(
                    $this->issueFactory->createIssueForObsoleteVariable(
                        $options,
                        $method,
                        $className,
                        $file,
                        $param->getContent()
                    )
                );
            }
        }
        if ($options['missing_variable'] !== null) {
            foreach ($this->differ->inSignatureOnly($tagsInDocblock, $signature) as $param) {
                $result->addIssue(
                    $this->issueFactory->createIssueForMissingVariable(
                        $options,
                        $method,
                        $className,
                        $file,
                        $param
                    )
                );
            }
        }

        return $result;
    }
}
