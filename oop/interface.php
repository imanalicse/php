<?php

/**
 * Interface provides only declaration of methods, no implementation detail
 * Interface cannot be instantiated
 * Interface variable must be constant
 * Method in interface must be public
 * To get the constant variable of interface use Scope regulation operator
 * We can create object from class but we cannot create object from interface
 */
class A
{
    public function display()
    {
        echo "This is the Display method of main Class A";
    }
}

interface I
{
    // variable should be always constant
    const COMPANY = "My company";

    //In interface Method must be public
    public function company();
    public function display();
}

class B extends A implements I
{
    // interface method must be implementation
    public function company()
    {
        echo "This is the Company method of child class";
    }

    // overriding method of main class A
    public function display()
    {
        echo "This is the Display method of child Class B";
    }
}

$obj =  new B();
$obj->display();

// to get the constant variable of interface
//echo B::COMPANY;
