# ZeroKelvin

Yet another object serializer except this one can deal with recursion

## What

It is an object serializer/unserializer. There are many, but this one can :
 * deal with internal php object
 * deal with recursion (I mean a pointer to an object, embedded in another place)
 * private properties of a parent class

## How

Based on php serialization. These two magic functions serialize() and unserialize()
are the only one that could come up with recursion AND hidden properties declared
as private in a parent class. var_export() does not export SplObjectStorage, 
var_dump() cannot deal with recursion and using reflection to recursively introspect 
parent class of an object is a pain in the ass.

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

## 