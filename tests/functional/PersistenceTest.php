<?php

/*
 * Mikromongo
 */

namespace tests\functional;

use Trismegiste\Mikromongo\Persistence\Connector;
use Trismegiste\Mikromongo\Persistence\Repository;
use Trismegiste\Mikromongo\Transformer\Serializer;
use Trismegiste\Mikromongo\Transformer\Unserializer;

/**
 * Persistence tests the persistence layer
 */
class PersistenceTest extends \PHPUnit_Framework_TestCase
{

    public function testWrite()
    {
        $cnx = new Connector(array('server' => 'localhost', 'database' => 'phpunit', 'collection' => 'mikro'));

        $repo = new Repository($cnx->getCollection(), new Serializer(), new Unserializer());
        $obj = new \stdClass();
        $obj->wesh = 42;
        $repo->persist($obj);
    }

}