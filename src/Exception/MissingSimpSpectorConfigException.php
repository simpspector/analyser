<?php

namespace SimpSpector\Analyser\Exception;

/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
class MissingSimpSpectorConfigException extends \Exception
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct("missing .simpspector.yml");
    }
}
