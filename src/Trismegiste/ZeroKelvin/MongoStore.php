<?php

/*
 * ZeroKelvin
 */

namespace Trismegiste\ZeroKelvin;

use Trismegiste\ZeroKelvin\MongoDb\Connector;
use Trismegiste\ZeroKelvin\MongoDb\Repository;

/**
 * MongoStore builds the repository against a MongoCollection
 */
class MongoStore
{

    protected $config;
    protected $defaultCfg = [
        'server' => 'localhost',
        'database' => 'zerokelvin',
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
     * @return \Trismegiste\ZeroKelvin\Persistence\RepositoryInterface
     */
    public function getRepository()
    {
        if (is_null($this->repository)) {
            $keyFactory = new MongoDb\MongoKeyFactory();
            $this->repository = new Repository($this->getCollection(), new Transformer($keyFactory));
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