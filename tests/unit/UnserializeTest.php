<?php

namespace tests\unit;

/**
 * UnserializeTest tests the unserializer service
 */
class UnserializeTest extends \PHPUnit_Framework_TestCase
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

        $spl1 = [
            new \ArrayObject(array(1, 2, 3)),
            ['--class' => 'ArrayObject', '--content' => new \MongoBinData('x:i:0;a:3:{i:0;i:1;i:1;i:2;i:2;i:3;};m:a:0:{}', 2)]
        ];

        $spl2 = new \SplObjectStorage();
        $spl2[new \stdClass()] = 123;
        $spl2[new \stdClass()] = 456;
        $flt2 = ['--class' => 'SplObjectStorage', '--content' => new \MongoBinData('x:i:2;O:8:"stdClass":0:{},i:123;;O:8:"stdClass":0:{},i:456;;m:a:0:{}', 2)];

        return [
            [$obj, ['--class' => 'stdClass', 'prop' => 123]],
            [$val, [
                    '--class' => 'stdClass',
                    'prop' => 123,
                    'tab' => [
                        1, true, "2\";2",
                        [3, ['--class' => 'stdClass']],
                        4
                    ]
                ]],
            $spl1,
            [$spl2, $flt2]
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

    /*
      public function testSpl()
      {
      $rest = '';
      $val = ;
      $val[new \stdClass()] = 456;
      $val[new \stdClass()] = 789;
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