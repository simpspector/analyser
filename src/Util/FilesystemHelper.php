<?php

namespace SimpSpector\Analyser\Util;

use Symfony\Component\Finder\Finder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class FilesystemHelper
{
    /**
     * @param string $path
     * @return string
     */
    public static function cleanPath($path, $file)
    {
        return ltrim(str_replace($path, '', $file), '/');
    }

    /**
     * @param string $path
     * @param string[] $folders
     * @param string $pattern
     * @return array
     */
    public static function findFiles($path, array $folders, $pattern = '*.php')
    {
        $cwd = getcwd();
        chdir($path);

        $finder = (new Finder())
            ->files()
            ->name($pattern)
            ->in($folders);

        $files = array_map(
            function ($file) {
                return $file->getRealpath();
            },
            iterator_to_array($finder)
        );

        chdir($cwd);

        return $files;
    }
}