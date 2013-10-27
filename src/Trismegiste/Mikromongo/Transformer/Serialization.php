<?php

/*
 * Mikromongo
 */

namespace Trismegiste\Mikromongo\Transformer;

/**
 * Serialization is a contract for transformations
 * based on php serialization
 */
interface Serialization
{

    const META_CLASS = '@class';
    const META_PRIVATE = '-';
    const META_PROTECTED = '';
    const META_PUBLIC = '+';
    const META_CUSTOM = '@content';
    const META_REFERENCE = '@ref';

}