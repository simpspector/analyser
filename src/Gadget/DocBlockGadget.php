<?php

namespace SimpSpector\Analyser\Gadget;

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
                $result->addIssue($this->createIssueForMissingDocblock($options, $method, $className, $file));
            }

            return $result;
        }

        $docBlock = new DocBlock($method['docblock']);

        $tagsInDocblock = $docBlock->getTags();
        $signature      = new FunctionSignature($method['signature']);

        if ($options['type_missmatch'] !== null) {
            foreach ($this->inBoth($tagsInDocblock, $signature) as $params) {

                /** @var DocBlock\Tag\ParamTag $docBlockParam */
                $docBlockParam = $params[0];
                /** @var FunctionParameter $functionParameter */
                $functionParameter = $params[1];

                if (! $this->equalTypes($functionParameter, $docBlockParam)) {
                    $result->addIssue(
                        $this->createIssueForMismatchingTypes(
                            $options, $method, $className, $file, $functionParameter, $docBlockParam->getType()
                        )
                    );
                }
            }
        }
        if ($options['obsolete_variable'] !== null) {
            foreach ($this->inDocblockOnly($tagsInDocblock, $signature) as $param) {
                $result->addIssue(
                    $this->createIssueForObsoleteVariable($options, $method, $className, $file, $param->getContent())
                );
            }
        }
        if ($options['missing_variable'] !== null) {
            foreach ($this->inSignatureOnly($tagsInDocblock, $signature) as $param) {
                $result->addIssue(
                    $this->createIssueForMissingVariable($options, $method, $className, $file, $param)
                );
            }
        }

        return $result;
    }

    /**
     * @param array $tagsInDocblock
     * @param FunctionSignature $signature
     *
     * @return array[]
     */
    private function inBoth(array $tagsInDocblock, FunctionSignature $signature)
    {
        $signatureParametersByName = [];
        foreach ($signature->parameters as $parameter) {
            $signatureParametersByName[$parameter->name] = $parameter;
        }

        /** @var DocBlock\Tag\ParamTag[] $docblockParams */
        $docblockParams = array_filter(
            $tagsInDocblock,
            function ($tag) use ($signature) {
                return (
                    ($tag instanceof DocBlock\Tag\ParamTag)
                    && in_array($tag->getVariableName(), $this->getFunctionParameterNames($signature))
                );
            }
        );

        $ret = [];
        foreach ($docblockParams as $parameter) {
            $ret[] = [
                $parameter,
                $signatureParametersByName[$parameter->getVariableName()]
            ];
        }

        return $ret;
    }

    /**
     * @param array $tagsInDocblock
     * @param FunctionSignature $signature
     *
     * @return DocBlock\Tag\ParamTag[]
     */
    private function inDocblockOnly(array $tagsInDocblock, FunctionSignature $signature)
    {
        return array_filter(
            $tagsInDocblock,
            function ($tag) use ($signature) {
                return (
                    ($tag instanceof DocBlock\Tag\ParamTag)
                    && ! in_array($tag->getVariableName(), $this->getFunctionParameterNames($signature))
                );
            }
        );
    }

    /**
     * @param array $tagsInDocblock
     * @param FunctionSignature $signature
     *
     * @return FunctionParameter[]
     */
    private function inSignatureOnly(array $tagsInDocblock, FunctionSignature $signature)
    {
        return array_filter(
            $signature->parameters,
            function (FunctionParameter $functionParameter) use ($tagsInDocblock) {
                return ! in_array($functionParameter->name, $this->getParameterNames($tagsInDocblock));
            }
        );
    }

    /**
     * @param array $tagsInDocblock
     *
     * @return string[]
     */
    private function getParameterNames(array $tagsInDocblock)
    {
        return array_map(
            function (DocBlock\Tag\ParamTag $tag) {
                return $tag->getVariableName();
            },
            array_filter(
                $tagsInDocblock,
                function ($tag) {
                    return $tag instanceof DocBlock\Tag\ParamTag;
                }
            )
        );
    }

    /**
     * @param FunctionSignature $signature
     *
     * @return array
     */
    private function getFunctionParameterNames(FunctionSignature $signature)
    {
        return array_map(
            function (FunctionParameter $parameter) {
                return $parameter->name;
            },
            $signature->parameters
        );
    }

    /**
     * @param FunctionParameter $functionParameter
     * @param DocBlock\Tag\ParamTag $docBlockParam
     *
     * @return bool
     */
    private function equalTypes(FunctionParameter $functionParameter, DocBlock\Tag\ParamTag $docBlockParam)
    {
        $functionType = ltrim($functionParameter->type, '\\');
        $docBlockType = ltrim($docBlockParam->getType(), '\\');
        if ($functionType == $docBlockType) {
            return true;
        }

        $docBlockTypes  = explode('|', $docBlockType);
        $primitiveTypes = ['string', 'int', 'integer', 'float', 'bool', 'boolean', 'null'];
        if (! $functionType and count(array_intersect($docBlockTypes, $primitiveTypes)) == count($docBlockTypes)) {
            return true;
        }

        if (($functionType === 'array') && (substr($docBlockType, -2, 2) === '[]')) {
            return true;
        }

        return false;
    }

    /**
     * @param array $options
     * @param array $method
     * @param string $className
     * @param string $file
     *
     * @return Issue
     */
    private function createIssueForMissingDocblock(array $options, array $method, $className, $file)
    {
        $issue = new Issue(
            $this,
            sprintf(
                '"%s" missing docblock',
                $className . ($className ? '::' : '') . $method['signature']
            )
        );
        $issue->setLevel($options['missing_docblock']);
        $issue->setLine($method['startLine']);
        $issue->setFile($file);

        return $issue;
    }

    /**
     * @param array $options
     * @param array $method
     * @param string $className
     * @param string $file
     * @param FunctionParameter $functionParameter
     * @param string $docBlockType
     *
     * @return Issue
     */
    private function createIssueForMismatchingTypes(
        array $options,
        array $method,
        $className,
        $file,
        FunctionParameter $functionParameter,
        $docBlockType
    )
    {
        $issue = new Issue(
            $this,
            sprintf(
                '"%s" variable %s has type %s in docblock',
                $className . ($className ? '::' : '') . $method['signature'],
                $functionParameter->name,
                $docBlockType
            )
        );
        $issue->setLevel($options['obsolete_variable']);
        $issue->setLine($method['startLine']);
        $issue->setFile($file);

        return $issue;
    }

    /**
     * @param array $options
     * @param array $method
     * @param string $className
     * @param string $file
     * @param string $docBlockContent
     *
     * @return Issue
     */
    private function createIssueForObsoleteVariable(array $options, array $method, $className, $file, $docBlockContent)
    {
        $issue = new Issue(
            $this,
            sprintf(
                '"%s" obsolete variable %s in docblock',
                $className . ($className ? '::' : '') . $method['signature'],
                $docBlockContent
            )
        );
        $issue->setLevel($options['obsolete_variable']);
        $issue->setLine($method['startLine']);
        $issue->setFile($file);

        return $issue;
    }

    /**
     * @param array $options
     * @param array $method
     * @param string $className
     * @param string $file
     * @param FunctionParameter $parameter
     *
     * @return Issue
     */
    private function createIssueForMissingVariable(
        array $options,
        array $method,
        $className,
        $file,
        FunctionParameter $parameter
    )
    {
        $issue = new Issue(
            $this,
            sprintf(
                '"%s" missing variable %s in docblock',
                $className . ($className ? '::' : '') . $method['signature'],
                $parameter->name
            )
        );
        $issue->setLevel($options['missing_variable']);
        $issue->setLine($method['startLine']);
        $issue->setFile($file);

        return $issue;
    }
}
