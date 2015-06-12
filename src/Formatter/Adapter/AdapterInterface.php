<?php

namespace SimpSpector\Analyser\Formatter\Adapter;

use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface AdapterInterface
{
    /**
     * @param Result $result
     * @return string
     */
    public function format(Result $result);

    /**
     * @return string
     */
    public function getName();
}
