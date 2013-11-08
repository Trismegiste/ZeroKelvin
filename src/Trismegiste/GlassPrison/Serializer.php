<?php

/*
 * GlassPrison
 */

namespace Trismegiste\GlassPrison\Transformer;

/**
 * Serializer is a serializer service
 * 
 * It serializes a non-object multidimensional array with magic keys to a php serialized string
 */
class Serializer implements Serialization
{

    /**
     * Transforms an full array tree with magic keys to a serialized string of objects
     * 
     * @param array $dump the array with array-transformed objects
     * 
     * @return string the result string which could be unserialized into objects
     */
    public function fromArray(array $dump)
    {
        $current = '';

        if (array_key_exists(self::META_CUSTOM, $dump)) {
            $fqcn = $dump[self::META_CLASS];
            $content = $dump[self::META_CUSTOM];
            $current = 'C:' . strlen($fqcn) . ':"' . $fqcn . '":' . strlen($content->bin) . ':{' . $content->bin;
        } else {
            // object or array ?
            if (array_key_exists(self::META_CLASS, $dump)) {
                $fqcn = $dump[self::META_CLASS];
                unset($dump[self::META_CLASS]);
                $current = 'O:' . strlen($fqcn) . ':"' . $fqcn . '":' . (count($dump)) . ":{";
            } else {
                $current = 'a:' . (count($dump)) . ":{";
            }
            // manage content assoc
            foreach ($dump as $key => $val) {
                // manage key
                if (isset($fqcn)) {
                    switch ($key[0]) {
                        case self::META_PRIVATE:
                            $key = "\000$fqcn\000" . substr($key, 1);
                            break;
                        case self::META_PUBLIC:
                            $key = substr($key, 1);
                            break;
                        default:
                            $key = "\000*\000" . $key;
                            break;
                    }
                }
                $current .= serialize($key);
                // manage value
                if (is_array($val)) {
                    $current .= $this->fromArray($val);
                } else {
                    if (is_object($val)) {
                        switch (get_class($val)) {
                            case 'MongoDate' :
                                if (isset($fqcn) && ($fqcn === 'DateTime')) {
                                    $val = date('Y-m-d H:i:s', $val->sec);
                                }
                                break;
                        }
                    }
                    $current.= serialize($val);
                }
            }
        }
        $current .= '}';

        return $current;
    }

}