<?php

namespace tests\unit;

use Trismegiste\ZeroKelvin\Serializer;

/**
 * SerializerTest tests the unserializer service
 */
class SerializerTest extends \PHPUnit_Framework_TestCase
{

    use DataProvider;

    protected $service;

    protected function setUp()
    {
        $this->service = new Serializer();
    }

    /**
     * @dataProvider objectProvider
     */
    public function testSerializationObject($src, $dst)
    {
        $this->assertEquals($src, unserialize($this->service->fromArray($dst)));
    }

}