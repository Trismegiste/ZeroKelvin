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
     * @dataProvider getObjectType
     */
    public function testSerializationObject($src, $dst)
    {
        $this->assertEquals($src, unserialize($this->service->fromArray($dst)));
    }

    public function testDateTime()
    {
        $flat = [
            Serializer::META_CLASS => 'stdClass',
            Serializer::META_UUID => 123,
            '+now' => [
                Serializer::META_CLASS => 'DateTime',
                Serializer::META_UUID => 456,
                '+date' => date('Y-m-d H:i:s'),
                '+timezone_type' => 3,
                '+timezone' => 'Europe/Paris',
            ]
        ];

        $obj = unserialize($this->service->fromArray($flat));
        $this->assertInstanceOf('stdClass', $obj);
        $this->assertInstanceOf('DateTime', $obj->now);
        $this->assertLessThan(1, $obj->now->diff(new \DateTime())->s);
    }

}