<?php

/*
 * GlassPrison
 */

namespace Trismegiste\GlassPrison;

/**
 * Unserializer is a unserializer service
 * 
 * It unserializes a string to a non-object multidimensional array
 */
class Unserializer implements Serialization
{

    protected $reference;
    protected $uuidFactory;

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
        return $this->recurUnserializeValue($str, $rest);
    }

    protected function recurUnserializeValue($str, &$rest)
    {
        if ($str[0] !== 'r') {
            $ptr = count($this->reference);
            $this->reference[$ptr] = null;
        }

        $value = $this->recurUnserializeData($str, $rest);

        if (isset($ptr)) {
            $this->reference[$ptr] = & $value;
        }

        return $value;
    }

    protected function recurUnserializeData($str, &$rest)
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
                $rest = substr($str, 2);
                return null;

            case 'a':
                $assoc = array();
                preg_match('#^a:(\d+):{(.*)#', $str, $extract);

                $len = $extract[1];
                $body = $extract[2];

                for ($idx = 0; $idx < $len; $idx++) {
                    // manage key                    
                    $key = $this->recurUnserializeData($body, $rest);
                    $body = $rest;
                    // manage value
                    $val = $this->recurUnserializeValue($body, $rest);
                    $assoc[$key] = $val;
                    $body = $rest;
                }
                $rest = substr($body, 1); // strip the }

                return $assoc;

            case 'O':
                preg_match('#^O:(\d+):"([^"]+)":(\d+):{(.*)#', $str, $extract);

                $className = $extract[2];
                $classLen = strlen($className);
                $len = $extract[3];
                $body = $extract[4];
                $objAssoc = [
                    self::META_CLASS => $className,
                    self::META_UUID => $this->uuidFactory->create()
                ];
                // we have more information on this value, we update it
                $this->reference[count($this->reference) - 1] = & $objAssoc;
                for ($idx = 0; $idx < $len; $idx++) {
                    // manage key
                    $key = $this->recurUnserializeData($body, $rest);
                    // manage access
                    $key = str_replace("\000", '#', $key);

                    $body = $rest;
                    // manage value
                    $val = $this->recurUnserializeValue($body, $rest);
                    $objAssoc[$key] = $val;
                    $body = $rest;
                }
                $rest = substr($body, 1); // strip the }

                return $objAssoc;

            case 'r':
            case 'R':
                preg_match('#^(r|R):(\d+);(.*)#', $str, $extract);
                $rest = $extract[3];

                return [self::META_REF => $this->reference[$extract[2]][self::META_UUID]];

            case 'C':
                preg_match('#^C:(\d+):"([^"]+)":(\d+):(.*)#', $str, $extract);

                $className = $extract[2];
                $rest = substr($extract[4], $extract[3] + 2);

                return [
                    self::META_CLASS => $className,
                    self::META_CUSTOM => substr($extract[4], 1, $extract[3])
                ];

            default:
                throw new \OutOfBoundsException("Fail to unserialize {$str[0]} type");
        }
    }

}