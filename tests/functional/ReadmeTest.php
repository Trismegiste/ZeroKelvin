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

    public function testRichDocument()
    {
        $this->expectOutputString('vader');

        $builder = new \Trismegiste\Mikromongo\Service();
        $repository = $builder->getRepository();
        // saving an object :
        $product = new LightSaber('red');
        $product->setOwner(new Owner('vader'));
        $repository->persist($product);
        $pk = (string) $product->getId();
        // retrieving an object by its pk :
        $found = $repository->findByPk($pk);
        echo $found->getOwnerName(); // => 'vader'
    }

}

// some example class
class LightSaber implements \Trismegiste\Mikromongo\Persistence\Persistable
{

    use \Trismegiste\Mikromongo\Persistence\PersistableImpl;

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