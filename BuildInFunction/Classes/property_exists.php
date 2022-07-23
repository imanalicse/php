<?php
/**
 * property_exists — Checks if the object or class has a property
 * property_exists(object|string $object_or_class, string $property): bool
 * Return Values: Returns true if the property exists, false if it doesn't exist or null in case of an error.
 */

 class myClass {
    public $mine;
    private $xpto;
    static protected $test;

    static function test() {
        var_dump(property_exists('myClass', 'xpto')); //true
    }
}

var_dump(property_exists('myClass', 'mine'));   //true
var_dump(property_exists(new myClass, 'mine')); //true
var_dump(property_exists('myClass', 'xpto'));   //true
var_dump(property_exists('myClass', 'bar'));    //false
var_dump(property_exists('myClass', 'test'));   //true
myClass::test();