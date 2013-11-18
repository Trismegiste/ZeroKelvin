# ZeroKelvin

Yet another object serializer except this one can deal with recursion

## What

It is an object serializer/unserializer. There are many, but this one can :
 * deal with internal php object
 * deal with recursion (I mean a pointer to an object, embedded in another place)
 * extract private properties of a parent class
 * create an object no matter the constructor is
 * Absolutly no contraints on how objects are designed

## Examples

### Object to arrays
```php
$transform = new \Trismegiste\ZeroKelvin\Transformer(new \Trismegiste\ZeroKelvin\UuidFactory());
$product = new LightSaber('red');
$product->setOwner(new Owner('vader'));
$dump = $transform->toArray($product);
print_r($dump);
// ouputs 
[
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
]
```

### Arrays to object
```php
$transform = new \Trismegiste\ZeroKelvin\Transformer(new \Trismegiste\ZeroKelvin\UuidFactory());
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
$product = $transform->fromArray($dump);
print_r($product);
// ouputs
tests\functional\LightSaber Object
(
    [color:protected] => red
    [owner:protected] => tests\functional\Owner Object
        (
            [name:protected] => vader
        )
)
```

See the [full test][1]

## How

Based on php serialization. Serialize() and unserialize()
are the only magic functions that could come up with recursion AND hidden 
properties declared as private in a parent class. 
Var_dump cannot deal with recursion, var_export cannot export SplObjectStorage 
and using reflection to recursively introspect parent classes of an object 
is a pain in the ass.

So I came up with this lib. Later, I have added a repository service to 
store those dumps into MongoDb.

## Why

It was a foolish attempt to make an ODM for MongoDb but I think 
it is unwise to use it for that purpose :
 * there is too much noise into objects
 * this is damn slow
 * no real error handling if objects would be updated in database
 * queries should be awful
 * updates are not possible

Anyway, this lib could be useful for tests, fast prototyping, simple app in 
CLI or some specific use cases like an asynchronous event queue. I have to
mention that entity loading from MongoDb are made in only two passes even with
complex tree structures with high depth. 

## What this library cannot do ?

This lib cannot handle references on scalar and array. It should be possible 
but the dumped array would carry too much noise (index, foreign key...)
to be really useful.

This lib also cannot store properly custom serialization (implementation of 
the Serializable php interface). Anyway those objects are stored "as is" so
you can restore them without problem. 

[1]: https://github.com/Trismegiste/ZeroKelvin/tree/master/tests/functional/DumperExampleTest.php#L39
