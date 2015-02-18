<?php

namespace SimpSpector\Analyser\Util;

use DavidBadura\MarkdownBuilder\MarkdownBuilder as BaseMarkdownBuilder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class MarkdownBuilder extends BaseMarkdownBuilder
{
    /**
     * @param string $code
     * @param string $lang
     * @param array $options
     * @return $this
     */
    public function code($code, $lang = '', array $options = [])
    {
        $attr = [];

        foreach ($options as $key => $value) {
            $attr[] = $key . ':' . $value;
        }

        return $this
            ->writeln('```' . $lang . ' ' . implode(' ', $attr))
            ->writeln($code)
            ->writeln('```')
            ->br();
    }
}