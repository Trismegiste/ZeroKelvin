<?php

namespace tests\unit;

use Trismegiste\Mikromongo\Transformer\Serializer;

/**
 * UnserializeTest tests the unserializer service
 */
class UnserializeTest extends \PHPUnit_Framework_TestCase
{

    use DataProvider;

    protected $service;

    protected function setUp()
    {
        $this->service = new Serializer();
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage type
     */
    public function testMalformed()
    {
        $this->service->unserialize('X');
    }

    /**
     * @dataProvider getInternalType
     */
    public function testSimpleCase($val)
    {
        $this->assertEquals($val, $this->service->unserialize(serialize($val)));
    }

    /**
     * @dataProvider getObjectType
     */
    public function testTransformedObject($src, $dst)
    {
        $this->assertEquals($dst, $this->service->unserialize(serialize($src)));
    }

    /**
     * @dataProvider getObjectType
     */
    public function testSerializationObject($src, $dst)
    {
        $this->assertEquals($src, unserialize($this->service->serialize($dst)));
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