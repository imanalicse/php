<?php

//https://www.youtube.com/watch?v=D55-kkS9XHI

class MainClass
{

    protected $var = "I like OOP";

    function __construct($test = '')
    {
        $this->var = $test ? $test : $this->var;
    }

    public function my_function()
    {
        echo $this->var;
    }

    function __destruct()
    {
        echo "<br/>Desctructor run";
    }
}

class ChildClass extends MainClass
{
    public function display()
    {
        echo $this->var;
    }
}

$obj = new ChildClass("Hi");
$obj->display();
//echo $obj->var;