<?php

namespace SimpSpector\Analyser\Gadget\DocBlockGadget;

use phpDocumentor\Reflection\DocBlock;
use SimpSpector\Analyser\Gadget\DocBlockGadget;
use SimpSpector\Analyser\Issue;

class IssueFactory
{
    /**
     * @var DocBlockGadget
     */
    private $gadget;

    /**
     * @param DocBlockGadget $gadget
     */
    public function __construct(DocBlockGadget $gadget)
    {
        $this->gadget = $gadget;
    }

    /**
     * @param array $options
     * @param array $method
     * @param string $className
     * @param string $file
     *
     * @return Issue
     */
    public function createIssueForMissingDocblock(array $options, array $method, $className, $file)
    {
        $issue = new Issue(
            $this->gadget,
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
     * @param DocBlock\Tag\ParamTag $docBlockParam
     *
     * @return Issue
     */
    public function createIssueForMismatchingTypes(
        array $options,
        array $method,
        $className,
        $file,
        FunctionParameter $functionParameter,
        DocBlock\Tag\ParamTag $docBlockParam
    )
    {
        $issue = new Issue(
            $this->gadget,
            sprintf(
                '"%s" variable %s has type %s in docblock',
                $className . ($className ? '::' : '') . $method['signature'],
                $functionParameter->name,
                $docBlockParam->getType()
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
    public function createIssueForObsoleteVariable(array $options, array $method, $className, $file, $docBlockContent)
    {
        $issue = new Issue(
            $this->gadget,
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
    public function createIssueForMissingVariable(
        array $options,
        array $method,
        $className,
        $file,
        FunctionParameter $parameter
    )
    {
        $issue = new Issue(
            $this->gadget,
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

    /**
     * @param array $options
     * @param array $method
     * @param string $className
     * @param string $file
     * @param FunctionParameter $functionParameter
     *
     * @return Issue
     */
    public function createIssueForMissingTypeInSignature(
        array $options,
        array $method,
        $className,
        $file,
        FunctionParameter $functionParameter
    )
    {
        $issue = new Issue(
            $this->gadget,
            sprintf(
                'Missing type for variable %s in docblock of "%s"',
                $functionParameter->name,
                $className
            )
        );
        $issue->setLevel($options['missing_type_in_signature']);
        $issue->setLine($method['startLine']);
        $issue->setFile($file);

        return $issue;
    }

    /**
     * @param array $options
     * @param array $method
     * @param string $className
     * @param string $file
     * @param DocBlock\Tag\ParamTag $docBlockParam
     *
     * @return Issue
     */
    public function createIssueForMissingTypeInDocBlock(
        array $options,
        array $method,
        $className,
        $file,
        DocBlock\Tag\ParamTag $docBlockParam
    )
    {
        $issue = new Issue(
            $this->gadget,
            sprintf(
                'Missing type for variable %s in docblock of "%s"',
                $docBlockParam->getName(),
                $className
            )
        );
        $issue->setLevel($options['missing_type_in_docblock']);
        $issue->setLine($method['startLine']);
        $issue->setFile($file);

        return $issue;
    }
}
