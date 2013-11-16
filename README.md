# ZeroKelvin

Yet another object serializer except this one can deal with recursion

## What

It is an object serializer/unserializer. There are many, but this one can :
 * deal with internal php object
 * deal with recursion (I mean a pointer to an object, embedded in another place)
 * extract private properties of a parent class
 * create an object no matter the constructor is

## Example

```php

```

## How

Based on php serialization. Serialize() and unserialize()
are the only magic functions that could come up with recursion AND hidden 
properties declared as private in a parent class. 
Var_dump cannot deal with recursion, var_export cannot export SplObjectStorage 
and using reflection to recursively introspect parent classes of an object 
is a pain in the ass.

So I came up with this lib.

## Why

It was a foolish attempt to make an ODM for MongoDb but I think 
it is unwise to use it for that purpose :
 * there is too much noise in objects
 * this is damn slow
 * no real error handling if objects would be updated in database
 * queries should be awful

Anyway, this lib could be useful for tests, fast prototyping, simple app in 
CLI or a model with a really painful mapping (a graph for example).

## What this library cannot do ?

This lib cannot handle references on scalar and array. It should be possible 
but the dumped array would carry too much noise (index, foreign key...)
to be really useful.

This lib also cannot store properly custom serialization (implementation of 
the Serializable php interface). Anyway those objects are stored "as is" so
you can restore them without problem. 

