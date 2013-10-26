<?php

namespace tests\fixtures;

use Trismegiste\Mikromongo\Persistence;

class Entity implements Persistence\Persistable
{

    use Persistence\PersistableImpl;

    private $answer;

    public function __construct()
    {
        $this->answer = 42;
    }

}