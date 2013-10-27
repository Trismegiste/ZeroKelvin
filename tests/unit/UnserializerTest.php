<?php

namespace tests\unit;

use Trismegiste\Mikromongo\Transformer\Unserializer;

/**
 * UnserializerTest tests the unserializer service
 */
class UnserializerTest extends \PHPUnit_Framework_TestCase
{

    use DataProvider;

    protected $service;

    protected function setUp()
    {
        $this->service = new Unserializer();
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage type
     */
    public function testMalformed()
    {
        $this->service->toArray('X');
    }

    /**
     * @dataProvider getInternalType
     */
    public function testSimpleCase($val)
    {
        $this->assertEquals($val, $this->service->toArray(serialize($val)));
    }

    /**
     * @dataProvider getObjectType
     */
    public function testTransformedObject($src, $dst)
    {
        $this->assertEquals($dst, $this->service->toArray(serialize($src)));
    }

    public function testDateTime()
    {
        $val = new \DateTime();
        $result = $this->service->toArray(serialize($val));
        $this->assertInstanceOf('MongoDate', $result['+date']);
        $this->assertEquals('DateTime', $result[Unserializer::META_CLASS]);
    }

    public function testMongoBinData()
    {
        $val = new \MongoBinData("yolo", 2);
        $this->assertInstanceOf('MongoBinData', $this->service->toArray(serialize($val)));
    }

    public function testMongoId()
    {
        $obj = new \MongoId();
        $dump = $this->service->toArray(serialize($obj));
        $this->assertInstanceOf('MongoId', $dump);
        $this->assertEquals($dump, $obj);
    }

    public function testCustom()
    {
        $this->service->toArray(serialize(new \tests\fixtures\Entity()));
    }

}