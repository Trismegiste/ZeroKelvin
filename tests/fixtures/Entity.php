<?php

namespace tests\fixtures;

class Entity
{

    private $answer;
    public $now;
    private $compound;
    protected $init = 1234;
    protected $embed;
    private $ref;
    protected $custom;

    public function __construct()
    {
        $this->answer = 42;
        $this->now = new \DateTime();
        $this->compound = [1, 4, 9];
        $this->init = 5678;
        $this->embed = new Access();
        $this->ref = $this->embed;
        $this->custom = new \ArrayObject([299, 792, 458]);
    }

    public function setAnswer($n)
    {
        $this->answer = $n;
    }

}