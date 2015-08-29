<?php

namespace SimpSpector\Analyser\Gadget\DocBlockGadget;

use phpDocumentor\Reflection\DocBlock;

class Differ
{
    /**
     * @param array $tagsInDocBlock
     * @param FunctionSignature $signature
     *
     * @return DocBlock\Tag\ParamTag[]
     */
    public function inDocBlockOnly(array $tagsInDocBlock, FunctionSignature $signature)
    {
        return array_filter(
            $tagsInDocBlock,
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
    public function inSignatureOnly(array $tagsInDocblock, FunctionSignature $signature)
    {
        return array_filter(
            $signature->parameters,
            function (FunctionParameter $functionParameter) use ($tagsInDocblock) {
                return ! in_array($functionParameter->name, $this->getParameterNames($tagsInDocblock));
            }
        );
    }

    /**
     * @param FunctionParameter $functionParameter
     * @param DocBlock\Tag\ParamTag $docBlockParam
     *
     * @return bool
     */
    public function equalTypes(FunctionParameter $functionParameter, DocBlock\Tag\ParamTag $docBlockParam)
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
     * @param array $tagsInDocblock
     * @param FunctionSignature $signature
     *
     * @return array[]
     */
    public function inDocBlockAndSignature(array $tagsInDocblock, FunctionSignature $signature)
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
     * @param array $tagsInDocBlock
     *
     * @return string[]
     */
    private function getParameterNames(array $tagsInDocBlock)
    {
        return array_map(
            function (DocBlock\Tag\ParamTag $tag) {
                return $tag->getVariableName();
            },
            array_filter(
                $tagsInDocBlock,
                function ($tag) {
                    return $tag instanceof DocBlock\Tag\ParamTag;
                }
            )
        );
    }
}
