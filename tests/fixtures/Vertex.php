<?php

namespace tests\fixtures;

class Vertex extends Root
{

    public $noise;
    protected $name;
    private $neighbor = [];

    public function __construct($n)
    {
        parent::__construct();
        $this->name = $n;
        $this->noise = rand();
    }

    public function add(Vertex $v)
    {
        $this->neighbor[] = $v;
    }

}