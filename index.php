<?php

/**
 * DateTimeDecorator docrates date
 */
class DateTimeWrapper implements JsonSerializable
{

    protected $wrapped;

    public function __construct(DateTime $d)
    {
        $this->wrapped = $d;
    }

    public function jsonSerialize()
    {
        $dump = get_object_vars($this->wrapped);
        $dump['--class'] = 'DateTime';

        return $dump;
    }

}

class MongoBinDataWrapper implements JsonSerializable
{

    protected $wrapped;

    public function __construct(MongoBinData $d)
    {
        $this->wrapped = $d;
    }

    public function jsonSerialize()
    {
        $dump = get_object_vars($this->wrapped);
        $dump['--class'] = 'MongoBinData';

        return $this->wrapped;
    }

}

trait JsonSerializableImpl
{

    protected function decorateObject(array& $arr)
    {
        foreach ($arr as $k => &$v) {
            if (is_object($v) && class_exists($found = get_class($v) . 'Wrapper')) {
                $v = new $found($v);
            }
        }
    }

    public function jsonSerialize()
    {
        $tab = get_object_vars($this);
        $this->decorateObject($tab);
        $tab['--class'] = get_called_class();

        return $tab;
    }

    public function wakeup(array $dump)
    {
        $this->__construct();
    }

}

class Img implements \JsonSerializable
{

    use JsonSerializableImpl;

    protected $src;

    public function __construct()
    {
        $this->src = 'http';
    }

}

class Content implements \JsonSerializable
{

    use JsonSerializableImpl;

    protected $body;
    protected $author;
    protected $option = array(1, 2, 3);
    protected $img;
    protected $created_at;
    protected $internalAo;
    protected $internalSpl;
    protected $blob;
    protected $self;

    public function __construct($b, $a)
    {
        $this->body = $b;
        $this->author = $a;
        $this->img = new Img();
        $this->created_at = new \DateTime();
        $this->internalAo = new ArrayObject($this->option);
        $this->internalSpl = new SplObjectStorage();
        $this->internalSpl[$this->img] = 640;
        $this->blob = new MongoBinData('avatar', 2);
        $this->self = $this;
    }

}

$obj = new Content('yop yop', 'flo');

//print_r(json_decode(json_encode($obj), true));

echo serialize($obj);

$restore = (unserialize('O:8:"DateTime":3:{s:4:"date";s:19:"2013-10-23 06:24:15";s:13:"timezone_type";i:3;s:8:"timezone";s:3:"UTC";}'));
//echo $restore->getTimestamp();

function phpUnserialize($str)
{
    switch($str[0]) {
        case 'O':
            $extract = array();
            preg_match('#^O:(\d+):"([^"]+)":(\d+):\{(.+)\}$#', $str, $extract);
            print_r($extract);
            for($idx = 0; $idx < $extract[3]; $idx++) {
                
            }
            break;
    }
}

phpUnserialize(serialize($obj));



foreach(str_split('abc') as $car) {
    echo $car;
}