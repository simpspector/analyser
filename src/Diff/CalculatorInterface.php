<?php

namespace SimpSpector\Analyser\Diff;

use SimpSpector\Analyser\Result as AnalyseResult;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface CalculatorInterface
{
    /**
     * @param AnalyseResult $from
     * @param AnalyseResult $to
     * @return Result
     */
    public function diff(AnalyseResult $from, AnalyseResult $to);
}
