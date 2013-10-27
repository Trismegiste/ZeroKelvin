<?php

/*
 * Mikromongo
 */

namespace tests\functional;

use Trismegiste\Mikromongo\Service;

/**
 * ServiceTest tests the service
 */
class ServiceTest extends \PHPUnit_Framework_TestCase
{

    public function testCreation()
    {
        $service = new Service();

        $this->assertInstanceOf('Trismegiste\Mikromongo\Persistence\RepositoryInterface', $service->getRepository());
        $this->assertInstanceOf('MongoCollection', $service->getCollection());
    }

}