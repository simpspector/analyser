<?php

namespace SimpSpector\Analyser\Formatter\Adapter;

use SimpSpector\Analyser\Result;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * @author Robin Willig <robin@dragonito.net>
 */
class XmlAdapter extends SerializerAdapter
{
    /**
     * @param Result $result
     * @return string
     */
    public function format(Result $result)
    {
        return $this->serializer->serialize($result, 'xml');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'xml';
    }
}
