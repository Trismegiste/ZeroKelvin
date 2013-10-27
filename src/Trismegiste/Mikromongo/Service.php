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
 * Service is the service of Persistence of Mikromongo
 */
class Service
{

    protected $config;
    protected $defaultCfg = [
        'server' => 'localhost',
        'database' => 'mikromongo',
        'collection' => 'entity'
    ];

    public function __construct(array $config = array())
    {
        $this->config = array_merge($this->defaultCfg, $config);
    }

    public function getRepository()
    {
        $cnx = new Connector($this->config);

        return new Repository($cnx->getCollection(), new Serializer(), new Unserializer());
    }

}