<?php

/*
 * Mikromongo
 */

namespace Trismegiste\Mikromongo\Transformer;

/**
 * Serializer is a serializer service
 */
class Serializer implements Serialization
{

    public function serialize(array $dump)
    {
        $current = '';
        if (array_key_exists(self::META_REFERENCE, $dump)) {
            $ref = $dump[self::META_REFERENCE];
            return key($ref) . ':' . current($ref) . ';';
        } else if (array_key_exists(self::META_CUSTOM, $dump)) {
            $fqcn = $dump[self::META_CLASS];
            $content = $dump[self::META_CUSTOM];
            $current = 'C:' . strlen($fqcn) . ':"' . $fqcn . '":' . strlen($content->bin) . ':{' . $content->bin;
        } else {
            if (array_key_exists(self::META_CLASS, $dump)) {
                $fqcn = $dump[self::META_CLASS];
                unset($dump[self::META_CLASS]);
                $current = 'O:' . strlen($fqcn) . ':"' . $fqcn . '":' . (count($dump)) . ":{";
            } else {
                $current = 'a:' . (count($dump)) . ":{";
            }
            foreach ($dump as $key => $val) {
                // manage key
                if (isset($fqcn)) {
                    switch ($key[0]) {
                        case self::META_PRIVATE:
                            $key = "\000$fqcn\000" . substr($key, 1);
                            break;
                        case self::META_PROTECTED:
                            $key = "\000*\000" . substr($key, 1);
                            break;
                    }
                }
                $current .= serialize($key);
                // manage value
                if (is_array($val)) {
                    $current .= $this->serialize($val);
                } else {
                    $current.= serialize($val);
                }
            }
        }
        $current .= '}';

        return $current;
    }

}