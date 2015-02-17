<?php

class Foo
{
    const VAR_DUMP = 'foo';

    public function __construct()
    {
        echo "var_dump";

        // @todo do something here

        echo "foo";
    }

    private function bar()
    {
        /**
         * some @todo just in the middle
         *
         * no real@todo wouldnt you say?
         *
         * todo without all that email stuff
         *
         * var_dump
         *
         * @todo this I need
         */

        iamok();

        var_dump();

        var_dump_extra();


        die();

        exit(10);

        $foo = <<<PHP

// @todo bar

PHP;
        extra_var_dump();
    }
}
