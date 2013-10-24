<?php

/*
 * Mikromongo
 */

namespace Trismegiste\Mikromongo;

/**
 * Serializer is a un/serialiazer service
 */
class Serializer
{

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
                    $key = $this->recurUnserializer($body, $rest);
                    $body = $rest;
                    $val = $this->recurUnserializer($body, $rest);
                    $assoc[$key] = $val;
                    $body = $rest;
                }
                $rest = substr($body, 1); // strip the }

                return $assoc;

            case 'O':
                preg_match('#^O:(\d+):"([^"]+)":(\d+):(.*)#', $str, $extract);

                $className = $extract[2];
                $objLen = $extract[3];
                $objBody = $extract[4];
                # A little hacky; plunders the Array unserialize logic.
                $objAssoc = $this->recurUnserializer("a:$objLen:$objBody", $rest);
                $objAssoc['--class'] = $className;

                return $objAssoc;

            default:
                throw new \Exception('Fail');
        }
    }

}