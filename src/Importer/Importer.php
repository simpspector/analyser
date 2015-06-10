<?php

namespace SimpSpector\Analyser\Importer;

use SimpSpector\Analyser\Importer\Adapter\AdapterInterface;
use SimpSpector\Analyser\Importer\Adapter\JsonAdapter;
use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Importer implements ImporterInterface
{
    /**
     * @var AdapterInterface[]
     */
    protected $adapters = [];

    /**
     * @param string $path
     * @return Result
     * @throws \Exception
     */
    public function import($path)
    {
        foreach ($this->adapters as $adapter) {
            if (!$adapter->support($path)) {
                continue;
            }

            return $adapter->import($path);
        }

        throw new \Exception('import failed');
    }

    /**
     * @param AdapterInterface $adapter
     * @throws \Exception
     */
    public function registerAdapter(AdapterInterface $adapter)
    {
        $this->adapters[] = $adapter;
    }
}