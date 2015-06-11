<?php

namespace SimpSpector\Analyser\Formatter;

use SimpSpector\Analyser\Formatter\Adapter\AdapterInterface;
use SimpSpector\Analyser\Formatter\Adapter\DetailAdapter;
use SimpSpector\Analyser\Formatter\Adapter\JsonAdapter;
use SimpSpector\Analyser\Formatter\Adapter\SummaryAdapter;
use SimpSpector\Analyser\Formatter\Adapter\XmlAdapter;
use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Formatter implements FormatterInterface
{
    /**
     * @var AdapterInterface[]
     */
    protected $adapters = [];

    /**
     * @param Result $result
     * @param string $format
     * @return string
     * @throws \Exception
     */
    public function format(Result $result, $format)
    {
        if (!isset($this->adapters[$format])) {
            throw new \Exception(sprintf('format "%s" are not supported', $format));
        }

        return $this->adapters[$format]->format($result);
    }

    /**
     * @param AdapterInterface $adapter
     * @throws \Exception
     */
    public function registerAdapter(AdapterInterface $adapter)
    {
        if (isset($this->adapters[$adapter->getName()])) {
            throw new \Exception(sprintf('formatter with the name "%s" exists already', $adapter->getName()));
        }

        $this->adapters[$adapter->getName()] = $adapter;
    }
}
