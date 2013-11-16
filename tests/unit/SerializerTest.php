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
        $generator = $this->getMock('Trismegiste\ZeroKelvin\UuidFactory');
        $generator->expects($this->any())
                ->method('getFieldName')
                ->will($this->returnValue('@uuid'));

        $this->service = new Serializer($generator);
    }

    /**
     * @dataProvider objectProvider
     */
    public function testSerializationObject($src, $dst)
    {
        $this->assertEquals($src, unserialize($this->service->fromArray($dst)));
    }

    /**
     * @dataProvider getSplType
     */
    public function testSpl($obj, $flat)
    {
        $result = unserialize($this->service->fromArray($flat));
        $this->assertInstanceOf('SplObjectStorage', $result->prop);
        $this->assertCount(2, $result->prop);
    }

}