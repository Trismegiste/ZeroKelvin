<?php

/*
 * Mikromongo
 */

namespace Trismegiste\Mikromongo;

/**
 * Serializer is a un/serializer service
 */
class Serializer
{

    const META_CLASS = '@class';
    const META_PRIVATE = '-';
    const META_PROTECTED = '#';
    const META_CUSTOM = '@content';
    const META_REFERENCE = '@ref';

    public function unserialize($str)
    {
        $rest = '';
        return $this->recurUnserializer($str, $rest);
    }

    protected function recurUnserializer($str, &$rest)
    {
        $extract = array();

        switch ($str[0]) {
            case 'b':
                preg_match('#^b:(\d);(.*)#', $str, $extract);
                $rest = $extract[2];
                return (bool) $extract[1];

            case 'i':
                preg_match('#^i:(\d+);(.*)#', $str, $extract);
                $rest = $extract[2];
                return (int) $extract[1];

            case 'd':
                preg_match('#^d:(\d+.\d+);(.*)#', $str, $extract);
                $rest = $extract[2];
                return (double) $extract[1];

            case 's':
                preg_match('#^s:(\d+):"(.*)#', $str, $extract);
                $rest = substr($extract[2], $extract[1] + 2); // strip " and ;
                if (!$rest) {
                    $rest = '';
                }
                return substr($extract[2], 0, $extract[1]);

            case 'N':
                return null;

            case 'a':
                $assoc = array();
                preg_match('#^a:(\d+):{(.*)#', $str, $extract);

                $len = $extract[1];
                $body = $extract[2];

                for ($idx = 0; $idx < $len; $idx++) {
                    // manage key                    
                    $key = $this->recurUnserializer($body, $rest);
                    $body = $rest;
                    // manage value
                    $val = $this->recurUnserializer($body, $rest);
                    $assoc[$key] = $val;
                    $body = $rest;
                }
                $rest = substr($body, 1); // strip the }

                return $assoc;

            case 'O':
                $objAssoc = array();
                preg_match('#^O:(\d+):"([^"]+)":(\d+):{(.*)#', $str, $extract);

                $className = $extract[2];
                $classLen = strlen($className);
                $len = $extract[3];
                $body = $extract[4];

                for ($idx = 0; $idx < $len; $idx++) {
                    // manage key
                    $key = $this->recurUnserializer($body, $rest);
                    // manage access
                    if ($key[0] === "\000") {
                        if ($key[1] === '*') {
                            $key = self::META_PROTECTED . substr($key, 3);
                        } else {
                            $key = self::META_PRIVATE . substr($key, 2 + $classLen);
                        }
                    }
                    $body = $rest;
                    // manage value
                    $val = $this->recurUnserializer($body, $rest);
                    $objAssoc[$key] = $val;
                    $body = $rest;
                }
                $rest = substr($body, 1); // strip the }
                $objAssoc[self::META_CLASS] = $className;

                return $objAssoc;

            case 'r':
            case 'R':
                preg_match('#^(r|R):(\d+);(.*)#', $str, $extract);
                $rest = $extract[3];

                return [self::META_REFERENCE => [$extract[1] => (int) $extract[2]]];

            case 'C':
                preg_match('#^C:(\d+):"([^"]+)":(\d+):(.*)#', $str, $extract);
                return [
                    self::META_CLASS => $extract[2],
                    self::META_CUSTOM => new \MongoBinData(substr($extract[4], 1, $extract[3]), \MongoBinData::BYTE_ARRAY)
                ];

            default:
                throw new \OutOfBoundsException("Fail to unserialize {$str[0]} type");
        }
    }

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