<?php

namespace tests\unit;

use Trismegiste\Mikromongo\Serializer;

/**
 * UnserializeTest tests the unserializer service
 */
class UnserializeTest extends \PHPUnit_Framework_TestCase
{

    protected $service;

    protected function setUp()
    {
        $this->service = new Serializer();
    }

    public function getInternalType()
    {
        $data = [
            false, true,
            123, 123.456,
            "illogical",
            null,
            [123], [123, 456],
            [[], 123, [456, [[[]]]], "w:;\":s:3:\"esh" => 789, "str" => "com\"bo", [[]]]
        ];
        $fixtures = [];
        foreach ($data as $val) {
            $fixtures[] = [$val];
        }

        return $fixtures;
    }

    public function getTransformedType()
    {
        $obj = new \stdClass();
        $obj->prop = 123;

        $val = clone $obj;
        $val->tab = array(1, true, "2\";2", array(3, new \stdClass()), 4);

        return [
            [$obj, [Serializer::META_CLASS => 'stdClass', 'prop' => 123]],
            [$val, [
                    Serializer::META_CLASS => 'stdClass',
                    'prop' => 123,
                    'tab' => [
                        1, true, "2\";2",
                        [3, [Serializer::META_CLASS => 'stdClass']],
                        4
                    ]
                ]]
        ];
    }

    /**
     * @dataProvider getInternalType
     */
    public function testSimpleCase($val)
    {
        $this->assertEquals($val, $this->service->unserialize(serialize($val)));
    }

    /**
     * @dataProvider getTransformedType
     */
    public function testTransformedObject($src, $dst)
    {
        $this->assertEquals($dst, $this->service->unserialize(serialize($src)));
    }

    public function testPropAccess()
    {
        $obj = new \tests\fixtures\Access();
        $flat = [
            Serializer::META_CLASS => 'tests\fixtures\Access',
            '-notInherited' => 111,
            '#inherited' => 222,
            'openbar' => 333
        ];

        $this->assertEquals($flat, $this->service->unserialize(serialize($obj)));
    }

    /*
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

    public function testRef()
    {
        $obj = new \stdClass();
        $obj->prop1 = 123;
        $obj->prop2 = array(0, 1, 2,);
        $obj->ref_this = $obj;
        $obj->prop3 = new \stdClass();
        $obj->prop4 = new \stdClass();
        $obj->ref_prop1 = &$obj->prop1;
        $obj->ref_prop2 = &$obj->prop2;
        $obj->ref_prop3 = $obj->prop3;
        $obj->ref_prop2_1 = &$obj->prop2[1];
        $obj->clone_ref_this = $obj->ref_this;
        $obj->ref_ref_this = &$obj->ref_this;
        $obj->prop3->inner_ref = $obj->prop4;

        // print_r($this->service->unserialize(serialize($obj)));
    }

    /**
     * @dataProvider getTransformedType
     */
    public function testSerializationObject($src, $dst)
    {
        $this->assertEquals($src, unserialize($this->service->serialize($dst)));
    }

    public function testSerializedPropAccess()
    {
        $obj = new \tests\fixtures\Access();
        $flat = [
            Serializer::META_CLASS => 'tests\fixtures\Access',
            '-notInherited' => 111,
            '#inherited' => 222,
            'openbar' => 333
        ];
        print_r($obj);
        print_r(unserialize($this->service->serialize($flat)));
    }

}