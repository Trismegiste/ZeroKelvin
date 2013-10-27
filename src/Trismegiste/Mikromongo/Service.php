<?php

/*
 * Mikromongo
 */

namespace Trismegiste\Mikromongo;

use Trismegiste\Mikromongo\Persistence\Connector;
use Trismegiste\Mikromongo\Transformer\Serializer;
use Trismegiste\Mikromongo\Transformer\Unserializer;
use Trismegiste\Mikromongo\Persistence\Repository;

/**
 * Service builds the repository against a MongoCollection
 */
class Service
{

    protected $config;
    protected $defaultCfg = [
        'server' => 'localhost',
        'database' => 'mikromongo',
        'collection' => 'entity'
    ];
    protected $collection;
    protected $repository;

    public function __construct(array $config = array())
    {
        $this->config = array_merge($this->defaultCfg, $config);
    }

    /**
     * Gets the current repository
     *  
     * @return \Trismegiste\Mikromongo\Persistence\RepositoryInterface
     */
    public function getRepository()
    {
        if (is_null($this->repository)) {
            $this->repository = new Repository($this->getCollection(), new Serializer(), new Unserializer());
        }
        return $this->repository;
    }

    /**
     * Gets the current collection
     * 
     * @return MongoCollection
     */
    public function getCollection()
    {
        if (is_null($this->collection)) {
            $cnx = new Connector($this->config);
            $this->collection = $cnx->getCollection();
        }

        return $this->collection;
    }

}