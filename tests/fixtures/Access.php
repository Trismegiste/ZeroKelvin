<?php

namespace tests\fixtures;

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
