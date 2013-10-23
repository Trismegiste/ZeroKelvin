<?php

function phpUnserialize($str, $ptr = 0)
{
    echo "start=" . $str . PHP_EOL;
    $raw = explode(':', $str, 3);

    switch ($raw[0]) {
        case 'b':
            return (bool) substr($raw[1], 0, -1);

        case 'i':
            return (int) substr($raw[1], 0, -1);

        case 'd':
            return (double) substr($raw[1], 0, -1);

        case 's':
            return substr($raw[2], 1, $raw[1]);

        case 'N;':
            return null;

        case 'a':
            $assoc = array();
            $len = $raw[1];
            $body = substr($raw[2], 1, -1);

            for ($idx = 0; $idx < $len; $idx++) {
                $matched = array();
                preg_match('/^(i:\d+;|s:\d+:".*?";)([NibdsaO].+)/', $body, $matched);
                echo "-$body-\n";
                $key = phpUnserialize($matched[1]);
                echo "key=$key" . PHP_EOL;
                $body = $matched[2];
                $val = phpUnserialize($body);
                print_r($val);
                echo PHP_EOL;
                $assoc[$key] = $val;
                $body = substr($body, strlen(serialize($val)));
                echo "reduced=$body" . PHP_EOL . PHP_EOL;
            }

            /*  $keyMatch = 'i:\d+;|s:\d+:".*?";';
              $valMatch = 'N;|b:[01];|i:\d+;|d:\d+.\d+;|s:\d+:".*?";|a:\d+:\{.*?\}';

              $matched = array();
              while (preg_match("/^($keyMatch)($valMatch)($keyMatch|$)(.*)/s", $body, $matched)) {
              $assoc[phpUnserialize($matched[1])] = phpUnserialize($matched[2]);
              $body = $matched[3] . $matched[4];
              echo $body . PHP_EOL;
              } */

            return $assoc;

        case 'O':
            $obj = $raw[2];
            $objList = explode(':', $obj, 3);

            $className = substr($objList[0], 1, -1);
            $objLen = $objList[1];
            $objBody = $objList[2];
            # A little hacky; plunders the Array unserialize logic.
            $objAssoc = phpUnserialize("a:$objLen:$objBody");
        //    $objAssoc['--class'] = $className;

            return $objAssoc;

        default:
            throw new \Exception('Fail');
    }
}

$obj = new \stdClass();
$obj->arf = 123;
$obj->tab = array(1, true, "2\";2", array(3, new \stdClass()), 4);


$s = serialize(array(1, array(array(2)), 3, array()));
$s = serialize($obj);
var_dump(phpUnserialize($s));