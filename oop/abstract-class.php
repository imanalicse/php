<?php

/*
 * Abstract class cannot be instantiated, abstract class can be only extended
 * any class that contains at least one abstract method must also be abstract
 * Abstract class's method cannot access directly
 *Different between abstract class and interface
 * 1. An abstract class does not provide full abstraction but an interface provide full abstraction.
 * This means an abstract classes can have non abstract member too
 * 2. In interface we cannot define of body of a function but in interface class we can define body of the function of on abstract method
 * 
 */

abstract class Car
{
    protected $name = 'Nissan';

    public function getName()
    {
        echo $this->name;
    }

    abstract protected function setName($name);
}

class BMW extends Car
{
    public function setName($name)
    {
        $this->name = $name;
    }
}


$obj = new BMW();
$obj->setName("BMD");
$obj->getName();
