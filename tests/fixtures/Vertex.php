<?php

namespace tests\fixtures;

class Vertex
{

    public $parent;
    protected $name;
    private $neighbor = [];

    public function __construct($n)
    {
        $this->name = $n;
    }

    public function add(Vertex $v)
    {
        $this->neighbor[] = $v;
    }

}