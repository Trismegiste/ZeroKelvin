<?php

namespace tests\fixtures;

class Access
{

    private $notInherited;
    protected $noise;
    protected $inherited;
    public $openbar;

    public function __construct()
    {
        $this->notInherited = 111;
        $this->inherited = 222;
        $this->openbar = 333;
    }

}
