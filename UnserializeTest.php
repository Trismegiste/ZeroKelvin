<?php

function phpUnserialize($str, &$rest)
{
    $extract = array();

    switch ($str[0]) {
        case 'b':
            preg_match('#^b:(\d);(.*)#', $str, $extract);
            $rest = $extract[2];
            return (bool) $extract[1];

        case 'i':
            preg_match('#^i:(\d+);(.*)#', $str, $extract);
            $rest = $extract[2];
            return (int) $extract[1];

        case 'd':
            preg_match('#^d:(\d+.\d+);(.*)#', $str, $extract);
            $rest = $extract[2];
            return (double) $extract[1];

        case 's':
            preg_match('#^s:(\d+):"(.*)#', $str, $extract);
            $rest = substr($extract[2], $extract[1] + 2); // strip " and ;
            if (!$rest) {
                $rest = '';
            }
            return substr($extract[2], 0, $extract[1]);

        case 'N':
            return null;

        case 'a':
            $assoc = array();
            preg_match('#^a:(\d+):{(.*)#', $str, $extract);

            $len = $extract[1];
            $body = $extract[2];

            for ($idx = 0; $idx < $len; $idx++) {
                $key = phpUnserialize($body, $rest);
                $body = $rest;
                $val = phpUnserialize($body, $rest);
                $assoc[$key] = $val;
                $body = $rest;
            }
            $rest = substr($body, 1); // strip the }

            return $assoc;

        case 'O':
            preg_match('#^O:(\d+):"([^"]+)":(\d+):(.*)#', $str, $extract);

            $className = $extract[2];
            $objLen = $extract[3];
            $objBody = $extract[4];
            # A little hacky; plunders the Array unserialize logic.
            $objAssoc = phpUnserialize("a:$objLen:$objBody", $rest);
            $objAssoc['--class'] = $className;

            return $objAssoc;

        default:
            throw new \Exception('Fail');
    }
}

/**
 * UnserializeTest is ...
 *
 * @author flo
 */
class UnserializeTest extends PHPUnit_Framework_TestCase
{

    public function testBoolTrue()
    {
        $rest = '';
        $this->assertTrue(phpUnserialize(serialize(true), $rest));
    }

    public function testBoolFalse()
    {
        $rest = '';
        $this->assertFalse(phpUnserialize(serialize(false), $rest));
    }

    public function testInteger()
    {
        $rest = '';
        $this->assertEquals(123, phpUnserialize(serialize(123), $rest));
    }

    public function testFloat()
    {
        $rest = '';
        $this->assertEquals(123.456, phpUnserialize(serialize(123.456), $rest));
    }

    public function testString()
    {
        $rest = '';
        $this->assertEquals("illogical", phpUnserialize(serialize("illogical"), $rest));
    }

    public function testNull()
    {
        $rest = '';
        $this->assertEquals(null, phpUnserialize(serialize(null), $rest));
    }

    public function testArray()
    {
        $rest = '';
        $this->assertEquals(array(123), phpUnserialize(serialize(array(123)), $rest));
    }

    public function testArray2()
    {
        $rest = '';
        $val = array(123, 456);
        $this->assertEquals($val, phpUnserialize(serialize($val), $rest));
    }

    public function testArrayEmbed()
    {
        $rest = '';
        $val = array(123, array(456), "w:;\":s:3:\"esh" => 789, "str" => "com\"bo", array(array()));
        $this->assertEquals($val, phpUnserialize(serialize($val), $rest));
    }

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

}