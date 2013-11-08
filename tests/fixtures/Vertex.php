<?php

namespace tests\fixtures;

class Vertex
{

    protected $name;
    protected $neighbor = [];

    public function __construct($n)
    {
        $this->name = $n;
    }

    public function add(Vertex $v)
    {
        $this->neighbor[] = $v;
    }

}