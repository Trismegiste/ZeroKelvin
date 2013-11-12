<?php

namespace tests\unit;

use Trismegiste\ZeroKelvin\Unserializer;

/**
 * UnserializerTest tests the unserializer service
 */
class UnserializerTest extends \PHPUnit_Framework_TestCase
{

    use DataProvider;

    protected $service;

    protected function setUp()
    {
        $generator = $this->getMock('Trismegiste\ZeroKelvin\UuidFactory');
        $generator->expects($this->any())
                ->method('create')
                ->will($this->returnValue('AAAA'));
        $this->service = new Unserializer($generator);
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
        $this->assertRegExp('#^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$#', $result['+date']);
        $this->assertEquals('DateTime', $result[Unserializer::META_CLASS]);
    }

    public function testCustom()
    {
        $this->service->toArray(serialize(new \tests\fixtures\Entity()));
    }

    /**
     * References are nulled
     */
    public function testTrackingReference()
    {
        $obj = new \stdClass();
        $obj->prop1 = 123;
        $obj->obj1 = new \stdClass();
        $obj->ref1 = $obj;
        $obj->ref2 = $obj->obj1;

        $flat = [
            Unserializer::META_CLASS => 'stdClass',
            '@uuid' => 'AAAA',
            '+obj1' => ['@classname' => 'stdClass', '@uuid' => 'AAAA'],
            '+prop1' => 123,
            '+ref1' => ['@ref' => 'AAAA'],
            '+ref2' => ['@ref' => 'AAAA']
        ];
        $dump = $this->service->toArray(serialize($obj));
        $this->assertEquals($flat, $dump);
    }

}