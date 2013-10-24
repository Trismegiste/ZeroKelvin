<?php

class Access
{

    private $nonInherited;
    protected $inherited;
    public $openbar;

    public function __construct()
    {
        $this->nonInherited = 123;
        $this->inherited = 222;
        $this->openbar = 333;
    }

}

$obj = new Access();
echo serialize($obj);

$transfo = 'O:6:"Access":3:{s:12:"nonInherited";i:123;s:9:"inherited";i:222;s:7:"openbar";i:333;}';
var_dump(unserialize($transfo));