<?php
/*
 * Abstract class cannot be instantiated
 *Different between abstract class and interface
 * 1. An abstract class does not provide full abstraction but an interface provide full abstraction.
 * This means an abstract classes can have non abstract member too
 * 2.
 * */
abstract class A {

    public function my_function(){
        echo "";
    }
}

//$obj = new A();
