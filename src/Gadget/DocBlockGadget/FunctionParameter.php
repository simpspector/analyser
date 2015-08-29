<?php

namespace SimpSpector\Analyser\Gadget\DocBlockGadget;

class FunctionParameter
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string|null
     */
    public $type;

    /**
     * @param string $name
     * @param string|null $type
     */
    public function __construct($name, $type = null)
    {
        $this->name = $name;
        $this->type = $type;
    }
}
