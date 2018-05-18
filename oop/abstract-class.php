<?php

/*
 * Abstract class cannot be instantiated, abstract class can be only extended
 * Abstract class's method cannot access directly
 *Different between abstract class and interface
 * 1. An abstract class does not provide full abstraction but an interface provide full abstraction.
 * This means an abstract classes can have non abstract member too
 * 2. In interface we cannot define of body of a function but in interface class we can define body of the function of on abstract method
 * 
 */

abstract class Car
{
    protected $name;

    public function getName()
    {
        echo $this->name;
    }

    abstract public function setName($name);
}

class BMW extends Car
{
    public function setName($name)
    {
        $this->name = $name;
    }
}


$obj = new BMW();
//$obj->setName("BMD");
$obj->getName();
