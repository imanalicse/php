<?php

/**
 * Declaring class properties or methods as static makes them accessible without needing an instantiation of the class
 * A property declared as static cannot be accessed with an instantiated class object (though a static method can)
 * $this is not available inside the method declared as static
 *In static method only static property available
 */

class Foo {

    public static $name = "Hello Name";

    public static function getName() {
        echo self::$name;
    }
}
Foo::getName();
//$obj = new Foo();
//echo Foo::$name;
