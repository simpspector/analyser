<?php

namespace SimpSpector\Analyser\Gadget\DocBlockGadget;

class FunctionSignature
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var FunctionParameter[]
     */
    public $parameters;

    /**
     * @param string $signature
     */
    public function __construct($signature)
    {
        $openBrace = strpos($signature, '(');

        $this->name = trim(substr($signature, 0, $openBrace));

        $parametersListString = substr($signature, $openBrace + 1, -1);
        if ($parametersListString) {
            foreach (explode(',', $parametersListString) as $parameterString) {
                $this->parameters[] = $this->parseParameterString($parameterString);
            }
        } else {
            $this->parameters = [];
        }
    }

    /**
     * @param string $parameterString
     *
     * @return FunctionParameter
     */
    private function parseParameterString($parameterString)
    {
        $parameterString = trim($parameterString);

        $i = strpos($parameterString, '=');
        if ($i !== false) {
            $parameterString = trim(substr($parameterString, 0, $i));
        }
        $parameterComponents = explode(' ', $parameterString);
        if (count($parameterComponents) == 1) {
            return new FunctionParameter($parameterComponents[0]);
        } else {
            return new FunctionParameter($parameterComponents[1], $parameterComponents[0]);
        }
    }
}
