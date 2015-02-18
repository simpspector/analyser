<?php

namespace SimpSpector\Analyser\Tests\Util;

use SimpSpector\Analyser\Util\MarkdownBuilder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class MarkdownBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testOutput()
    {
        $markdown = <<<TXT
```php line:5 file:foo.php
foo
```
TXT;

        $output = (new MarkdownBuilder())->code('foo', 'php', ['line' => 5, 'file' => 'foo.php']);

        $this->assertEquals($markdown, $output);
    }
}