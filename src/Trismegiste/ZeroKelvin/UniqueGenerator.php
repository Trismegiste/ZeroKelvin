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

    public function create();

    public function getFieldName();
}