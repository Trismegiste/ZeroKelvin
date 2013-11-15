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
    protected $generator;

    protected function setUp()
    {
        $this->generator = $this->getMock('Trismegiste\ZeroKelvin\UuidFactory');
        $this->service = new Unserializer($this->generator);
    }

    /**
     * @dataProvider objectProvider
     */
    public function testSimpleObject($simple, $expected)
    {
        $this->generator->expects($this->exactly(count($expected)))
                ->method('create')
                ->will($this->onConsecutiveCalls('AAAA', 'AAAB', 'AAAC'));

        $this->assertEquals($expected, $this->service->toArray(serialize($simple)));
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage type
     */
    public function testMalformed()
    {
        $this->service->toArray('X');
    }

}