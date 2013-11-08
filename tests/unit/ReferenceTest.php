<?php

/*
 * GlassPrison
 */

namespace tests\unit;

use tests\fixtures\Vertex;

/**
 * ReferenceTest tests the unserialization of reference
 */
class ReferenceTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;

    protected function setUp()
    {
        $this->sut = new \Trismegiste\GlassPrison\Unserializer;
    }

    public function testCycle()
    {
        $vertices = array_map(function($v) {
                    return new Vertex($v);
                }, range(0, 3));

        $vertices[0]->add($vertices[1]);
        $vertices[0]->add($vertices[2]);
        $vertices[0]->add($vertices[3]);
        $vertices[1]->add($vertices[3]);
        $vertices[2]->add($vertices[3]);
        $vertices[3]->add($vertices[0]);

        $str = serialize($vertices[0]);
        echo $str;
        print_r($this->sut->toArray($str));
    }

}