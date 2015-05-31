<?php

namespace SimpSpector\Analyser\Util;

use DavidBadura\MarkdownBuilder\MarkdownBuilder as BaseMarkdownBuilder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class MarkdownBuilder extends BaseMarkdownBuilder
{
    /**
     * @param string $header
     * @return $this
     */
    public function h1($header)
    {
        $header = trim($header);

        return $this
            ->writeln('<comment>' . $header . '</comment>')
            ->writeln('<comment>' . str_repeat("=", mb_strlen($header)) . '</comment>')
            ->br();
    }

    /**
     * @param string $header
     * @return $this
     */
    public function h2($header)
    {
        $header = trim($header);

        return $this
            ->writeln('<comment>' . $header . '</comment>')
            ->writeln('<comment>' . str_repeat("-", mb_strlen($header)) . '</comment>')
            ->br();
    }

    /**
     * @param string $header
     * @return $this
     */
    public function h3($header)
    {
        $header = trim($header);

        return $this
            ->writeln('<info>### ' . $header . '</info>')
            ->br();
    }

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