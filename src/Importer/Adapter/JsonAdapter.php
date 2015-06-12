<?php

namespace SimpSpector\Analyser\Importer\Adapter;

use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class JsonAdapter extends SerializerAdapter
{
    /**
     * @param string $path
     * @return Result
     */
    public function import($path)
    {
        return $this->serializer->deserialize(file_get_contents($path), 'SimpSpector\Analyser\Result', 'json');
    }

    /**
     * @param string $path
     * @return bool
     */
    public function support($path)
    {
        return preg_match('/\.json$/', $path);
    }
}
