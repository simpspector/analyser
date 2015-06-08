<?php

class Foo
{
    /**
     * @param string|null $baz
     *
     * @return string
     */
    public function bar($baz = null)
    {
        return $baz ? 'yes' : 'no';
    }
}
