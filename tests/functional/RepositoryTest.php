<?php

/*
 * ZeroKelvin
 */

namespace tests\functional;

use Trismegiste\ZeroKelvin\MongoDb\Repository;
use Trismegiste\ZeroKelvin\MongoDb\Connector;
use Trismegiste\ZeroKelvin\Transformer;
use Trismegiste\ZeroKelvin\MongoDb\MongoKeyFactory;
use tests\fixtures\Entity;

/**
 * RepositoryTest tests the repository
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{

    protected $collection;
    protected $repository;

    protected function setUp()
    {
        $cnx = new Connector(array('server' => 'localhost', 'database' => 'phpunit', 'collection' => 'zero'));
        $this->collection = $cnx->getCollection();
        $this->repository = new Repository($this->collection, new Transformer(new MongoKeyFactory()));
    }

    public function testWrite()
    {
        $obj = new Entity();
        $lastInsert = $this->repository->persist($obj);

        $this->assertNotNull($lastInsert);

        return $lastInsert;
    }

    /**
     * @depends testWrite
     */
    public function testRead($pk)
    {
        $raw = $this->collection->findOne(['_id' => $pk]);
        $this->assertEquals(5678, $raw['init']);

        return $pk;
    }

    /**
     * @depends testRead
     */
    public function testFind($pk)
    {
        $expected = new Entity();
        $found = $this->repository->findByPk($pk);
        $this->assertEquals($expected, $found);

        return $pk;
    }

    /**
     * @expectedException Trismegiste\ZeroKelvin\MongoDb\NotFoundException
     */
    public function testNotFound()
    {
        $this->repository->findByPk("4274c645631b6f8c1a000008");
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage contain
     */
    public function testInvalidRoot()
    {
        $this->repository->createFromDb([[]]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage match
     */
    public function testInvalidCount()
    {
        $this->repository->createFromDb([[Repository::FIELD_FOREIGN => [1]]]);
    }

}