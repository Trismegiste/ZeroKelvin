# Mikromongo

A MongoDb micro-ODM for micro-framework

## Why "Micro" ?

because :

 * zero config (if localhost)
 * zero class to extend, even abstract one
 * zero repository classes
 * zero property mapping
 * zero annotation, yml, xml, cache nor proxy
 * zero doc to browse (except this file)

## What

It is an Object Document Mapper for MongoDb. It is based on php internal serialization.
It is intended for rich documents and complex model. I'm fed up with the Anemic
Model Anti-pattern produced by most of ORM/ODM because the 
"Database Driven Development" is a bad workflow. 

We should design classes and model how we think the business and in no way 
how one ORM wants to implement its object <=> database mapping. 
The learning curve is awful (to get perfs) and we suffer too much constraints
to design our model. 

On the other
hand, PHP can serialize and store any complex object even with cyclic references 
in session without coding anything. Do you see the gap ?

So I design this lib. [DokudokiBundle][1] was a first attempt but it is more suited
for a full stack framework due to its complication. This ODM is simpler.

The goal is a tool ready to use in 5 minutes, like microframeworks 
which doesn't need extended doc reading and tons of boilerplate code in
php, yml and xml.

## How

```php
    $builder = new \Trismegiste\Mikromongo\Service();
    $repository = $builder->getRepository();
    // saving an object :
    $product = new LightSaber('red');
    $repository->persist($product);
    $pk = (string) $product->getId();
    // retrieving an object by its pk :
    $found = $repository->findByPk($pk);
    echo $found->getColor(); // => 'red'
```

The object $product must implement Persistable and use the trait PersistableImpl.
See the [full example][2] in functional tests.

Anything else is optional. You can make cleaning and waking-up with magic methods
__sleep() and __wakeup().

## Features

 * No mapping needed
 * Total freedom on model constructors
 * Total freedom on model inheritance
 * Embedded and complex objects design handling
 * DateTime are converted to MongoDate for queries against the collection
 * MongoBinData are stored as-is
 * around 170 NCLOC !

## Limitations

 * Implementations of the interface Serializable are stored as-is
 * No cyclic reference can be stored

[1]: http://github.com
[2]: http://github.com