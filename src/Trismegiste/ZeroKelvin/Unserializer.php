<?php

/*
 * ZeroKelvin
 */

namespace Trismegiste\ZeroKelvin;

/**
 * Unserializer is a unserializer service
 * 
 * It unserializes a string to a non-object multidimensional array
 */
class Unserializer implements Serialization
{

    protected $reference;
    protected $uuidFactory;
    protected $flatList;

    public function __construct(UniqueGenerator $fac)
    {
        $this->uuidFactory = $fac;
    }

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
        $this->reference = [null];
        $this->flatList = [];
        $ret = $this->recurUnserializeValue($str, $rest);
        $last = array_pop($this->flatList);
        $last['@foreign'] = array_keys($this->flatList);
        array_unshift($this->flatList, $last);
        $ret = array_values($this->flatList);
        //print_r($ret);
        return $ret;
    }

    protected function recurUnserializeValue($str, &$rest)
    {
        if ($str[0] !== 'r') {
            $ptr = count($this->reference);
            $this->reference[$ptr] = null;
            $value = &$this->reference[$ptr];
        } else {
            $value = null;
        }

        $this->recurUnserializeData($str, $rest, $value);

        if ($str[0] == 'O') {
            $pk = $value[self::META_UUID];
            $this->flatList[$pk] = $value;
            return [self::META_REF => $pk];
        } else {
            return $value;
        }
    }

    protected function recurUnserializeData($str, &$rest, &$newValue)
    {
        $extract = array();

        switch ($str[0]) {
            case 'b':
                preg_match('#^b:(\d);(.*)#', $str, $extract);
                $rest = $extract[2];
                $newValue = (bool) $extract[1];
                break;

            case 'i':
                preg_match('#^i:(\d+);(.*)#', $str, $extract);
                $rest = $extract[2];
                $newValue = (int) $extract[1];
                break;

            case 'd':
                preg_match('#^d:(\d+.\d+);(.*)#', $str, $extract);
                $rest = $extract[2];
                $newValue = (double) $extract[1];
                break;

            case 's':
                preg_match('#^s:(\d+):"(.*)#', $str, $extract);
                $rest = substr($extract[2], $extract[1] + 2); // strip " and ;
                if (!$rest) {
                    $rest = '';
                }
                $newValue = substr($extract[2], 0, $extract[1]);
                break;

            case 'N':
                $rest = substr($str, 2);
                $newValue = null;
                break;

            case 'a':
                $newValue = array();
                preg_match('#^a:(\d+):{(.*)#', $str, $extract);

                $len = $extract[1];
                $body = $extract[2];

                for ($idx = 0; $idx < $len; $idx++) {
                    // manage key    
                    $key = null;
                    $this->recurUnserializeData($body, $rest, $key);
                    $body = $rest;
                    // manage value
                    $val = $this->recurUnserializeValue($body, $rest);
                    $newValue[$key] = $val;
                    $body = $rest;
                }
                $rest = substr($body, 1); // strip the }
                break;

            case 'O':
                preg_match('#^O:(\d+):"([^"]+)":(\d+):{(.*)#', $str, $extract);

                $className = $extract[2];
                $classLen = strlen($className);
                $len = $extract[3];
                $body = $extract[4];
                $newValue = [
                    self::META_CLASS => $className,
                    self::META_UUID => $this->uuidFactory->create()
                ];

                for ($idx = 0; $idx < $len; $idx++) {
                    // manage key
                    $key = null;
                    $this->recurUnserializeData($body, $rest, $key);
                    // manage access
                    if ($key[0] === "\000") {
                        if ($key[1] === '*') {
                            $key = substr($key, 3);
                        } else {
                            $key = str_replace("\000", self::META_PRIVATE, $key);
                        }
                    } else {
                        $key = self::META_PUBLIC . $key;
                    }

                    $body = $rest;
                    // manage value
                    $val = $this->recurUnserializeValue($body, $rest);
                    $newValue[$key] = $val;
                    $body = $rest;
                }
                $rest = substr($body, 1); // strip the }
                break;

            case 'r':
                preg_match('#^r:(\d+);(.*)#', $str, $extract);
                $rest = $extract[2];

                $newValue = [self::META_REF => $this->reference[$extract[1]][self::META_UUID]];
                break;

            case 'C':
                preg_match('#^C:(\d+):"([^"]+)":(\d+):(.*)#', $str, $extract);

                $className = $extract[2];
                $rest = substr($extract[4], $extract[3] + 2);

                $newValue = [
                    self::META_CLASS => $className,
                    self::META_CUSTOM => substr($extract[4], 1, $extract[3])
                ];
                break;

            default:
                throw new \OutOfBoundsException("Fail to unserialize {$str[0]} type");
        }
    }

}