<?php

namespace SimpSpector\Analyser\Loader;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface LoaderInterface
{
    /**
     * @param string $path
     * @return array
     */
    public function load($path);
}
