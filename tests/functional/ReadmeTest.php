<?php

/*
 * Mikromongo
 */

namespace tests\functional;

/**
 * ReadmeTest is the example from README.md
 */
class ReadmeTest extends \PHPUnit_Framework_TestCase
{

    public function testExample()
    {
        $this->expectOutputString('red');
        
        $builder = new \Trismegiste\Mikromongo\Service();
        $repository = $builder->getRepository();
        // saving an object :
        $product = new LightSaber('red');
        $repository->persist($product);
        $pk = (string) $product->getId();
        // retrieving an object by its pk :
        $found = $repository->findByPk($pk);
        echo $found->getColor(); // => 'red'
    }

}

// some example class
class LightSaber implements \Trismegiste\Mikromongo\Persistence\Persistable
{

    use \Trismegiste\Mikromongo\Persistence\PersistableImpl;

    protected $color;

    public function __construct($c)
    {
        $this->color = $c;
    }

    public function getColor()
    {
        return $this->color;
    }

}