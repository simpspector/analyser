<?php

namespace SimpSpector\Analyser\Formatter\Adapter;

use SimpSpector\Analyser\Result;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class JsonAdapter extends SerializerAdapter
{
    /**
     * @param Result $result
     * @return string
     */
    public function format(Result $result)
    {
        return $this->serializer->serialize($result, 'json', ['json_encode_options' => JSON_PRETTY_PRINT]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'json';
    }
}