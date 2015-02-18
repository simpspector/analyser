<?php

namespace SimpSpector\Analyser\Util;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class SnippetHelper
{
    /**
     * @param string $source
     * @param int $line
     * @param int $around
     * @return string
     */
    public static function createSnippet($source, $line, $around = 5)
    {
        $rows = explode(PHP_EOL, $source);

        $offset = max($line - $around, 1) - 1;
        $length = $around * 2 + 1;
        if ($line + $length > count($rows)) {
            $length = count($rows) - $offset;
        }

        $slicedSource = array_slice($rows, $offset, $length);

        return implode(PHP_EOL, $slicedSource);
    }

    /**
     * @param string $file
     * @param int $line
     * @param int $around
     * @return string
     */
    public static function createSnippetByFile($file, $line, $around = 5)
    {
        return self::createSnippet(file_get_contents($file), $line, $around);
    }
}
