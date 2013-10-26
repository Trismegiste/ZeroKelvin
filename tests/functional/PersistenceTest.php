<?php

/*
 * Mikromongo
 */

namespace tests\functional;

use Trismegiste\Mikromongo\Persistence\Connector;
use Trismegiste\Mikromongo\Persistence\Repository;
use Trismegiste\Mikromongo\Transformer\Serializer;
use Trismegiste\Mikromongo\Transformer\Unserializer;
use tests\fixtures\Entity;

/**
 * Persistence tests the persistence layer
 */
class PersistenceTest extends \PHPUnit_Framework_TestCase
{

    protected $repository;

    protected function setUp()
    {
        $cnx = new Connector(array('server' => 'localhost', 'database' => 'phpunit', 'collection' => 'mikro'));
        $this->repository = new Repository($cnx->getCollection(), new Serializer(), new Unserializer());
    }

    public function testReadWrite()
    {
        $obj = new Entity();
        $this->repository->persist($obj);

        print_r($this->repository->findByPk("526be431631b6fd009000000"));
    }

}