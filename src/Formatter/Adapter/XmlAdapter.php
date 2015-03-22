<?php

namespace SimpSpector\Analyser\Formatter\Adapter;

use SimpSpector\Analyser\Result;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @author Robin Willig <robin@dragonito.net>
 */
class XmlAdapter implements AdapterInterface
{
    /**
     * @param Result $result
     */
    public function format(Result $result)
    {
        $serializer = new Serializer([new GetSetMethodNormalizer()], [new XmlEncoder()]);
        echo $serializer->serialize($result, 'xml');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'xml';
    }
}
