<?php

/*
 * Persistence
 */

namespace Trismegiste\Mikromongo\Persistence;

/**
 * A concrete connector against MongoDB. It's a wrapper to encapsulate config
 * 
 * This simple class mainly because mongo extension is subject to many changes
 */
class Connector
{

    protected $paramValid;

    public function __construct(array $param)
    {
        $this->paramValid = $param;
    }

    /**
     * Returns the mongo collection with the parameters set in constructor
     *
     * @return \MongoCollection
     */
    public function getCollection()
    {
        $cnx = new \MongoClient('mongodb://' . $this->paramValid['server']);

        return $cnx->selectCollection($this->paramValid['database'], $this->paramValid['collection']);
    }

}
