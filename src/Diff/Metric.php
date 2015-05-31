<?php

namespace SimpSpector\Analyser\Diff;

use SimpSpector\Analyser\Metric as AnalyseMetric;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Metric
{
    /**
     * @var AnalyseMetric
     */
    public $from;

    /**
     * @var AnalyseMetric
     */
    public $to;

    /**
     * @var float
     */
    public $diff;
}