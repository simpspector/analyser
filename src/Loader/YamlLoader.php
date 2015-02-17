<?php

namespace SimpSpector\Analyser\Loader;

use SimpSpector\Analyser\Exception\MissingSimpSpectorConfigException;
use Symfony\Component\Yaml\Yaml;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class YamlLoader implements LoaderInterface
{
    /**
     * @param string $path
     * @throws \Exception
     * @return array
     */
    public function load($path)
    {
        if (!file_exists($path)) {
            throw new MissingSimpSpectorConfigException();
        }

        return Yaml::parse(file_get_contents($path));
    }
}
