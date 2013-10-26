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

    /*

      public function testDateTime()
      {
      $rest = '';
      $val = new \DateTime();
      echo serialize($val);
      print_r(phpUnserialize(serialize($val), $rest));
      }

      public function testMongoBin()
      {
      $rest = '';
      $val = new \MongoBinData("yolo", 2);
      echo serialize($val);
      print_r(phpUnserialize(serialize($val), $rest));
      }

     */
}