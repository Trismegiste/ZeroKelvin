<?php

namespace tests\fixtures;

class Root
{

    private $notInherited;

    public function __construct()
    {
        $this->notInherited = rand();
    }

}