<?php

/*
 * GlassPrison
 */

namespace Trismegiste\GlassPrison;

/**
 * Serializer is a serializer service
 * 
 * It serializes a non-object multidimensional array with magic keys to a php serialized string
 */
class Serializer implements Serialization
{

    protected $reference;

    /**
     * Transforms an full array tree with magic keys to a serialized string of objects
     * 
     * @param array $dump the array with array-transformed objects
     * 
     * @return string the result string which could be unserialized into objects
     */
    public function fromArray(array $dump)
    {
        $this->reference = [null];
        return $this->recursivFromArray($dump);
    }

    protected function recursivFromArray(array $dump)
    {
        if (array_key_exists(self::META_REF, $dump)) {
            $uuid = $dump[self::META_REF];
            $found = array_search($uuid, $this->reference);
            if (false !== $found) {
                return 'r:' . $found . ';';
            } else {
                throw new \InvalidArgumentException("uuid $uuid not found");
            }
        }

        $current = '';
        if (array_key_exists(self::META_CUSTOM, $dump)) {
            $fqcn = $dump[self::META_CLASS];
            $content = $dump[self::META_CUSTOM];
            $current = 'C:' . strlen($fqcn) . ':"' . $fqcn . '":' . strlen($content) . ':{' . $content;
        } else {
            // object or array ?
            if (array_key_exists(self::META_CLASS, $dump)) {
                $fqcn = $dump[self::META_CLASS];
                unset($dump[self::META_CLASS]);
                $this->reference[] = $dump[self::META_UUID];
                unset($dump[self::META_UUID]);
                $current = 'O:' . strlen($fqcn) . ':"' . $fqcn . '":' . (count($dump)) . ":{";
            } else {
                $current = 'a:' . (count($dump)) . ":{";
                $this->reference[] = null;
            }
            // manage content assoc
            foreach ($dump as $key => $val) {
                // manage key
                if (isset($fqcn)) {
                    $key = str_replace('#', "\000", $key);
                }
                $current .= serialize($key);
                // manage value
                if (is_array($val)) {
                    $current .= $this->recursivFromArray($val);
                } else {
                    $this->reference[] = null;
                    $current.= serialize($val);
                }
            }
        }
        $current .= '}';

        return $current;
    }

}