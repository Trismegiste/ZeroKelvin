<?php

/*
 * Mikromongo
 */

namespace Trismegiste\Mikromongo\Persistence;

/**
 * PersistableImpl is an implementation for interface Persistable
 */
trait PersistableImpl
{

    protected $_id;

    public function setId(\MongoId $pk)
    {
        $this->_id = $pk;
    }

    public function getId()
    {
        return $this->_id;
    }

}