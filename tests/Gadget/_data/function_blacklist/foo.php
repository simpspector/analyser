<?php

class Foo
{
    const VAR_DUMP = 'foo';

    public function __construct()
    {
        echo "var_dump";

        // some comment

        echo "foo";
    }

    private function bar()
    {
        /**
         * A large comment
         *
         * including var_dump()
         *
         * todo and die() and exit()
         *
         * echo "foo";
         *
         *
         */

        iamok();

        var_dump();

        var_dump_extra();


        die();

        exit(10);

        $foo = <<<PHP

// @todo bar

PHP;
        var_dump();
        \extra_var_dump();
    }
}
