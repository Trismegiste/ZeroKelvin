<?php

/*
 * ZeroKelvin
 */

namespace Trismegiste\ZeroKelvin;

/**
 * UniqueGenerator is a contract for a generator of unique id
 */
interface UniqueGenerator
{

    /**
     * Creates a new primary key
     * 
     * @return mixed a new unique identifier
     */
    public function create();

    /**
     * Gets the field name of the unique identifier
     * 
     * @return string field name
     */
    public function getFieldName();
}