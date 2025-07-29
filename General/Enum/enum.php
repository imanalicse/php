<?php
/**
 * https://www.youtube.com/watch?v=5Cgio2OfOYk
 *
 * Enums are declared with the enum keyword, followed by the name of the Enum.
 * An Enum can optionally declare string or int as backed values.
 * Enums can also extend a class and/or implement interfaces.
 * Backed Enum cases and values must be unique
 * Enums allow methods
 *
 * Enum Does not allow:
 * Constructors and Destructors
 * Instance and properties
 * cloning because cases are singleton object
 * Magic Methods are not allowed except
 * __call, __callStatic, and __invoke
 *You cannot instantiation enum case via directly or via reflection api
 *
 * Enum Allows
 * Public, Private & protected methods
 * Static methods and event Constants
 *Enum can implement interface
 * Enum can use Traits (without properties)
 */

namespace App\General\Enum;
enum Suit
{
    case Hearts;
    case Diamonds;
    case Clubs;
    case Spades;
}

// Enums behave similar to classes when they are used with functions that support inspecting classes and objects.
var_dump(gettype(Suit::Clubs)); // "object"
//
//is_object(Suit::Spades); // true
//is_a(Suit::Clubs, Suit::class); // true
//
//get_class(Suit::Clubs); // "Suit"
//get_debug_type(Suit::Clubs); // "Suit"
//
//Suit::Clubs instanceof Suit; // true
//Suit::Clubs instanceof UnitEnum; // true
//Suit::Clubs instanceof object; // false

enum HTTPStatus: int
{
    case OK = 200;
    case ACCESS_DENIED = 403;
    case NOT_FOUND = 404;

    public function label(): string
    {
        return static::getLabel($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            HTTPStatus::OK => 'OK',
            HTTPStatus::ACCESS_DENIED => 'Access Denied',
            HTTPStatus::NOT_FOUND => 'Page Not Found',
        };
    }
}

// echo HTTPStatus::ACCESS_DENIED->label(); // "Access Denied"
// echo HTTPStatus::getLabel(HTTPStatus::ACCESS_DENIED); // "Access Denied"