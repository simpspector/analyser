<?php

class Foo
{
    /**
     * @param \object $baz
     *
     * @return string
     */
    public function bar(Foo $baz)
    {
        return $baz . ' ';
    }
}
