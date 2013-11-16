<?php

/*
 * ZeroKelvin
 */

namespace tests\unit;

use Trismegiste\ZeroKelvin\Transformer;

/**
 * TransformerTest tests the transformer
 */
class TransformerTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;

    protected function setUp()
    {
        $this->sut = new Transformer(new \Trismegiste\ZeroKelvin\UuidFactory());
    }

    public function testCycle()
    {
        $obj = new \stdClass();
        $this->assertEquals($obj, $this->sut->fromArray($this->sut->toArray($obj)));
    }

}