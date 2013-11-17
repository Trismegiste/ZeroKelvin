<?php

/*
 * ZeroKelvin
 */

namespace Trismegiste\ZeroKelvin\MongoDb;

use Trismegiste\ZeroKelvin\UuidFactory;

/**
 * MongoKeyFactory is a key factory for mongodb
 */
class MongoKeyFactory extends UuidFactory
{

    public function getFieldName()
    {
        return '_id';
    }

}