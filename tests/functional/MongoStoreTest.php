<?php

/*
 * ZeroKelvin
 */

namespace tests\functional;

use Trismegiste\ZeroKelvin\MongoStore;

/**
 * MongoStoreTest tess the builder of service for persistence
 */
class MongoStoreTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;

    protected function setUp()
    {
        $this->sut = new MongoStore();
    }

    public function testBuild()
    {
        $this->assertInstanceOf('Trismegiste\ZeroKelvin\RepositoryInterface', $this->sut->getRepository());
        $this->assertInstanceOf('MongoCollection', $this->sut->getCollection());
    }

}