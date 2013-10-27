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

    protected $collection;
    protected $repository;

    protected function setUp()
    {
        $cnx = new Connector(array('server' => 'localhost', 'database' => 'phpunit', 'collection' => 'mikro'));
        $this->collection = $cnx->getCollection();
        $this->repository = new Repository($this->collection, new Serializer(), new Unserializer());
    }

    public function testWrite()
    {
        $obj = new Entity();
        $this->repository->persist($obj);

        $this->assertNotNull($obj->getId());
        $this->assertInstanceOf('MongoId', $obj->getId());

        return (string) $obj->getId();
    }

    /**
     * @depends testWrite
     */
    public function testRead($pk)
    {
        $raw = $this->collection->findOne(['_id' => new \MongoId($pk)]);
        $this->assertEquals(222, $raw['embed']['inherited']);

        return $pk;
    }

    /**
     * @depends testRead
     */
    public function testFind($pk)
    {
        $expected = new Entity();
        $expected->setId(new \MongoId($pk));
        $found = $this->repository->findByPk($pk);
        $this->assertEquals($expected, $found);

        return $pk;
    }

    /**
     * @depends testFind
     */
    public function testUpdate($pk)
    {
        $expected = new Entity();
        $expected->setId(new \MongoId($pk));
        $found = $this->repository->findByPk($pk);
        $found->setAnswer(2001);
        $this->repository->persist($found);
        $raw = $this->collection->findOne(['_id' => new \MongoId($pk)]);
        $this->assertEquals(2001, $raw['-answer']);
        $this->assertEquals(new \MongoId($pk), $raw['_id']);
    }

}