<?php

/*
 * ZeroKelvin
 */

namespace Trismegiste\ZeroKelvin;

/**
 * Transformer is a service for transforming complex tree structure of objects
 * into a list of array with key, classname and reference
 */
class Transformer
{

    protected $serializer;
    protected $unserializer;

    public function __construct(UniqueGenerator $keygen)
    {
        $this->serializer = new Serializer($keygen);
        $this->unserializer = new Unserializer($keygen);
    }

    /**
     * Returns a list of objects transformed into array
     *  
     * @param object $obj
     * 
     * @return array
     */
    public function toArray($obj)
    {
        if (!is_object($obj)) {
            throw new \InvalidArgumentException(__METHOD__ . " can only transform objects");
        }

        return $this->unserializer->toArray(serialize($obj));
    }

    public function fromArray(array $arr)
    {
        return unserialize($this->serializer->fromArray($arr));
    }

}