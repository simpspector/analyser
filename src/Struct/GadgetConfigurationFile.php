<?php

/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpSpector\Analyser\Struct;

class GadgetConfigurationFile
{
    /**
     * @var string
     */
    public $filename;

    /**
     * @var string
     */
    public $content;

    public function __construct($filename, $content)
    {
        $this->filename = $filename;
        $this->content = $content;
    }
}
