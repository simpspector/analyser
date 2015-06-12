<?php

namespace SimpSpector\Analyser\Formatter;

use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface FormatterInterface
{
    /**
     * @param Result $result
     * @param string $format
     * @return string
     */
    public function format(Result $result, $format);
}
