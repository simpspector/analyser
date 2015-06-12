<?php

namespace SimpSpector\Analyser\Diff;

use SimpSpector\Analyser\Issue;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Result
{
    /**
     * @var Issue[]
     */
    public $newIssues = [];

    /**
     * @var Issue[]
     */
    public $resolvedIssues = [];

    /**
     * @var Metric[]
     */
    public $metricChanges = [];
}
