<?php

class Foo
{
    /**
     *
     */
    public function bar()
    {
        return array_filter(
            [1, 2, 3],
            function ($elem) {
                return $elem > 1;
            }
        );
    }
}
