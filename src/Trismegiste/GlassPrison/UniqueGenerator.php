<?php

/*
 * GlassPrison
 */

namespace Trismegiste\GlassPrison;

/**
 * UniqueGenerator is a contract for a generator of unique id
 */
interface UniqueGenerator
{

    public function create();
}