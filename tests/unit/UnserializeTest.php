<?php

/**
 * UnserializeTest tests the unserialiazer service
 */
class UnserializeTest extends PHPUnit_Framework_TestCase
{

    protected $service;

    protected function setUp()
    {
        $this->service = new \Trismegiste\Mikromongo\Serializer();
    }

    public function getInternalType()
    {
        $data = [
            false, true,
            123, 123.456,
            "illogical",
            null,
            [123], [123, 456],
            [123, [456], "w:;\":s:3:\"esh" => 789, "str" => "com\"bo", [[]]]
        ];
        $fixtures = [];
        foreach ($data as $val) {
            $fixtures[] = [$val];
        }

        return $fixtures;
    }

    /**
     * @dataProvider getInternalType
     */
    public function testSimpleCase($val)
    {
        $this->assertEquals($val, $this->service->unserialize(serialize($val)));
    }

    /*
      public function testSimpleObject()
      {
      $rest = '';
      $val = new \stdClass();
      $val->arf = 123;
      $this->assertEquals(array('--class' => 'stdClass', 'arf' => 123), phpUnserialize(serialize($val), $rest));
      }

      public function testComplexObject()
      {
      $rest = '';
      $val = new \stdClass();
      $val->arf = 123;
      $val->tab = array(1, true, "2\";2", array(3, new \stdClass()), 4);

      $this->assertEquals(array(
      '--class' => 'stdClass',
      'arf' => 123,
      'tab' => array(
      1, true, "2\";2",
      array(3, array('--class' => 'stdClass')),
      4
      )
      ), phpUnserialize(serialize($val), $rest));
      }

      public function testArrayObject()
      {
      $rest = '';
      $val = new \ArrayObject(array(1, 2, 3));
      echo serialize($val);
      print_r(phpUnserialize(serialize($val), $rest));
      }

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

      public function testSpl()
      {
      $rest = '';
      $val = new \SplObjectStorage();
      $val[new \stdClass()] = 456;
      $val[new \stdClass()] = 789;
      echo serialize($val);
      print_r(phpUnserialize(serialize($val), $rest));
      }

      public function testRef()
      {
      $rest = '';
      $val = new \stdClass();
      $val->self = $val;
      echo serialize($val);
      print_r(phpUnserialize(serialize($val), $rest));
      }
     */
}