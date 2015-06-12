<?php

namespace SimpSpector\Analyser\Loader;

use SimpSpector\Analyser\Exception\MissingSimpSpectorConfigException;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class LegacyFilenameLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param string $path
     * @return array
     */
    public function load($path)
    {
        try {
            return $this->loader->load($path);
        } catch (MissingSimpSpectorConfigException $e) {
            $path = str_replace('.simpspector.yml', 'simpspector.yml', $path);

            return $this->loader->load($path);
        }
    }
}
