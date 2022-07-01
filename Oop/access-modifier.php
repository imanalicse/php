<?php
/**
 * https://www.youtube.com/watch?v=D55-kkS9XHI
 * Public method and property can be access from its main class, derive class and outside of class
 * Protected property and method can be accessed from its main class and derive class
 * Private property and method can access only its main class
 */

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