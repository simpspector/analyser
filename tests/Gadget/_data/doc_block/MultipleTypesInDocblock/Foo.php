<?php

class Foo
{
    /**
     * @param string|int $baz
     *
     * @return string
     */
    public function bar($baz = null)
    {
        return $baz ? 'yes' : 'no';
    }

    /**
     * @param string|null $baz
     *
     * @return string
     */
    public function baz($baz = null)
    {
        return $baz ? 'yes' : 'no';
    }
}
