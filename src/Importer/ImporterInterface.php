<?php

namespace SimpSpector\Analyser\Importer;

use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface ImporterInterface
{
    /**
     * @param string $path
     * @return Result
     */
    public function import($path);
}
