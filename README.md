# Mikromongo

A MongoDb micro-ODM for micro-framework

## Why "Micro" ?

because :

 * zero config (if localhost)
 * zero class to extend, even abstract one !
 * zero entity repositories
 * zero property mapping
 * zero doc to browse (except this file)

## What

It is an Object Document Mapper for MongoDb. It is based on php internal serialization.
It is intended for rich documents and complex model because I'm fed up with the Anemic
Model Anti-pattern produced by most of ORM/ODM.

Because the "Database Driven Engineering" is no longer a solution, I think we should
design classes and model how we think the business and in no way how your ORM implements
the object <-> database mapping. I don't care if I must add and name my getter/setter
for the ORM to understand the mapping, adding gazillions of complex annotations,
 validators and create dumb constructors (without argument). 

On the other
hand, PHP can serialize and store any complex object even with cyclic references 
in session without coding anything. Do you see the gap ?

So I design this lib. DokudokiBundle was a first attempt but it had some
limitation that production reveals. This ODM is simpler and more powerful.

The goal is a tool ready to use in 5 minutes, like microframeworks 
which doesn't need extended doc reading and tons of boilerplate code in
php, yml or xml.

## TODO
