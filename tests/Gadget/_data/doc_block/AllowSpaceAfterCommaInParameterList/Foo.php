<?php

class Foo
{
    /**
     * @param ExecutorInterface $executor
     * @param LoaderInterface $loader
     */
    public function __construct(ExecutorInterface $executor, LoaderInterface $loader)
    {
        $this->executor = $executor;
        $this->loader   = $loader;
    }
}
