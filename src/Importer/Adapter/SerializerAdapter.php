<?php

namespace SimpSpector\Analyser\Importer\Adapter;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

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
        $this->serializer = SerializerBuilder::create()->build();
    }
}