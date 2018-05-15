<?php
class MyClass {

    private $var = "I like OOP";

    function __construct($test='')
    {
        $this->var = $test;
    }

    public function my_function() {
        echo $this->var;
    }
}

class NewClass extends MyClass {

}

$myClass = new MyClass("Hi");
$myClass->my_function();