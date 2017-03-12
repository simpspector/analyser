<?php

/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpSpector\Analyser\Struct;

class ConfigurationFile
{
    /**
     * @var string
     */
    public $filename;

    /**
     * @var string
     */
    public $content;

    public function _construct($filename, $content)
    {
        $this->filename = $filename;
        $this->content = $content;
    }
}
