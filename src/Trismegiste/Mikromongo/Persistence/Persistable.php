<?php

/*
 * Dokudokibundle
 */

namespace Trismegiste\Mikromongo\Persistence;

/**
 * Means this object has a primary key
 */
interface Persistable
{

    function getId();

    function setId(\MongoId $pk);
}
