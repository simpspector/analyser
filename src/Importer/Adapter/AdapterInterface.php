<?php

namespace SimpSpector\Analyser\Importer\Adapter;

use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface AdapterInterface
{
    /**
     * @param string $path
     * @return Result
     */
    public function import($path);

    /**
     * @param string $path
     * @return bool
     */
    public function support($path);
}
