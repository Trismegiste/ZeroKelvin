<?php

/*
 * functional
 */

namespace tests\functional;

use Trismegiste\ZeroKelvin\Transformer;

/**
 * DumperExample gives example of the serializing process
 */
class DumperExampleTest extends \PHPUnit_Framework_TestCase
{

    protected $transform;

    protected function setUp()
    {
        $generator = $this->getMock('Trismegiste\ZeroKelvin\UuidFactory');
        $generator->expects($this->any())
                ->method('getFieldName')
                ->will($this->returnValue('@uuid'));

        $generator->expects($this->any())
                ->method('create')
                ->will($this->onConsecutiveCalls('5b0294f7-65dd-4b17-bcbf-cd1923983649', 'dc969571-bf05-420f-a466-1d971dbd9c7b'));

        $this->transform = new Transformer($generator);
    }

    /**
     * Transforms a complex object with non-empty constructor to
     * a recursive array
     */
    public function testSerialize()
    {
        $product = new LightSaber('red');
        $product->setOwner(new Owner('vader'));
        $dump = $this->transform->toArray($product);
        $this->assertEquals([
            [
                '@classname' => 'tests\\functional\\LightSaber',
                'owner' => [ '@ref' => 'dc969571-bf05-420f-a466-1d971dbd9c7b'],
                '@uuid' => '5b0294f7-65dd-4b17-bcbf-cd1923983649',
                'color' => 'red'
            ],
            [
                '@classname' => 'tests\\functional\\Owner',
                '@uuid' => 'dc969571-bf05-420f-a466-1d971dbd9c7b',
                'name' => 'vader'
            ]
                ], $dump);
    }

    /**
     * Creates a complex object with non-empty constructor from
     * a recursive array
     */
    public function testUnserialize()
    {
        $dump = [
            [
                '@classname' => 'tests\\functional\\LightSaber',
                'owner' => [ '@ref' => 'dc969571-bf05-420f-a466-1d971dbd9c7b'],
                '@uuid' => '5b0294f7-65dd-4b17-bcbf-cd1923983649',
                'color' => 'red'
            ],
            [
                '@classname' => 'tests\\functional\\Owner',
                '@uuid' => 'dc969571-bf05-420f-a466-1d971dbd9c7b',
                'name' => 'vader'
            ]
        ];
        $product = $this->transform->fromArray($dump);
        $this->assertInstanceOf(__NAMESPACE__ . '\LightSaber', $product);
        $this->assertEquals('red', $product->getColor());
        $this->assertEquals('vader', $product->getOwnerName());
    }

}

//////////////////////////////
// some example class
class LightSaber
{

    protected $color;
    protected $owner;

    public function __construct($c)
    {
        $this->color = $c;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function setOwner(Owner $own)
    {
        $this->owner = $own;
    }

    public function getOwnerName()
    {
        return $this->owner->getName();
    }

}

class Owner
{

    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

}