<?php

/*
 * Mikromongo
 */

namespace Trismegiste\Mikromongo\Transformer;

/**
 * Unserializer is a unserializer service
 * 
 * It unserializes a string to a non-object multidimensional array
 */
class Unserializer implements Serialization
{

    /**
     * Transforms a serialized string into an array
     * 
     * @param string $str the php serialized string
     * 
     * @return array a full array tree with object transformed into array with magic key
     */
    public function toArray($str)
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

                // handling special object for MongoDb
                switch ($className) {
                    case 'DateTime':
                        $objAssoc['date'] = new \MongoDate(strtotime($objAssoc['date']));
                        break;

                    case 'MongoBinData':
                        $objAssoc = new \MongoBinData($objAssoc['bin'], $objAssoc['type']);
                        break;
                }

                return $objAssoc;

            case 'r':
            case 'R':
                preg_match('#^(r|R):(\d+);(.*)#', $str, $extract);
                $rest = $extract[3];

                return [self::META_REFERENCE => [$extract[1] => (int) $extract[2]]];

            case 'C':
                preg_match('#^C:(\d+):"([^"]+)":(\d+):(.*)#', $str, $extract);

                $className = $extract[2];
                if ($className === 'MongoId') {
                    return new \MongoId(substr($extract[4], 1, $extract[3]));
                }

                return [
                    self::META_CLASS => $className,
                    self::META_CUSTOM => new \MongoBinData(substr($extract[4], 1, $extract[3]), \MongoBinData::BYTE_ARRAY)
                ];

            default:
                throw new \OutOfBoundsException("Fail to unserialize {$str[0]} type");
        }
    }

}