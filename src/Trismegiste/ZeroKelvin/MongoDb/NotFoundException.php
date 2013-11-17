<?php

/**
 * Persistence
 */

namespace Trismegiste\ZeroKelvin\MongoDb;

/**
 * NotFoundException if an exception thrown when no document was found
 */
class NotFoundException extends \RuntimeException
{

    /**
     * constructor
     * 
     * @param string $pk the pk not found in database
     */
    public function __construct($pk)
    {
        parent::__construct("$pk was not found");
    }

}
