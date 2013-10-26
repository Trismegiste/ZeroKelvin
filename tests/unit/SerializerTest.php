<?php

namespace tests\unit;

use Trismegiste\Mikromongo\Transformer\Serializer;

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
            'now' => [
                Serializer::META_CLASS => 'DateTime',
                'date' => new \MongoDate(),
                'timezone_type' => 3,
                'timezone' => 'Europe/Paris'
            ]
        ];

        $obj = unserialize($this->service->fromArray($flat));
        $this->assertInstanceOf('stdClass', $obj);
        $this->assertInstanceOf('DateTime', $obj->now);
        $this->assertLessThan(1, $obj->now->diff(new \DateTime())->s);
    }

    public function testMongoBinData()
    {
        $flat = [
            Serializer::META_CLASS => 'stdClass',
            'img' => new \MongoBinData('yolo', \MongoBinData::BYTE_ARRAY)
        ];

        $obj = unserialize($this->service->fromArray($flat));
        $this->assertInstanceOf('stdClass', $obj);
        $this->assertInstanceOf('MongoBinData', $obj->img);
        $this->assertEquals('yolo', $obj->img->bin);
    }

}