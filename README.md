# ZeroKelvin

Yet another object serializer except this one can deal with recursion

## What

It is an object serializer/unserializer. There are many, but this one can :
 * deal with internal php object
 * deal with recursion (I mean a pointer to an object, embedded in another place)
 * private properties of a parent class

## How

Based on php serialization. Serialize() and unserialize()
are the only magic functions that could come up with recursion AND hidden properties declared
as private in a parent class. var_dump cannot deal with recursion, 
var_export cannot export SplObjectStorage and using reflection to introspect 
parent classes of an object would be a pain in the ass.

So I came up with this lib.

## Why

It was a foolish attempt to make a magical ODM for MongoDb but I think 
it is unwise to use it for that purpose :
 * there is too much noise in objects
 * this is damn slow
 * no real error handling if objects would be updated in database
 * queries should be awful

Anyway, this lib could be useful for tests, fast prototyping, simple app in 
CLI or a model with a really painful mapping (a graph for example).

## What this lib cannot do ?

Reference on scalar and array. It would be possible but the dumped array would
carry too much noise in it to be more useful than the original serialize stored 
in a flat text file for example. Simply because each value must embed a abstract 
reference index, so each value become an array. Perhaps on a latter release...