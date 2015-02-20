<?php

namespace SimpSpector\Analyser\Formatter\Adapter;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
abstract class SerializerAdapter implements AdapterInterface
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     *
     */
    public function __construct()
    {
        $this->serializer = new Serializer([new GetSetMethodNormalizer()], [new JsonEncoder()]);
    }
}