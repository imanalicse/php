<?php

namespace App\General\Enum;

enum OrderStatus : string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    // Any string can be set here
    public function label(): string {
        return match($this) {
            self::PENDING => self::PROCESSING->value,
            self::PROCESSING => 'The request is underway',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function description(): string {
        return match($this) {
            self::PENDING => 'Awaiting review, approval',
            self::PROCESSING => 'The request is underway and actively being worked on',
            self::SHIPPED => ' The order has been dispatched and is on its way to the delivery address',
            self::DELIVERED => 'The order has reached the customer and is confirmed as received',
            self::CANCELLED => 'The order or request has been terminated and will not be processed further',
        };
    }

    // get all scalar values as array
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function forSelect(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_column(self::cases(), 'name')
        );
    }
}

$label = OrderStatus::PROCESSING->label(); // The request is underway
$value = OrderStatus::PROCESSING->value; // processing
$name = OrderStatus::PROCESSING->name; // PROCESSING
$description = OrderStatus::PROCESSING->description(); // Awaiting review, approval
$values = OrderStatus::values();

$enum_from = OrderStatus::from('processing'); //  throws an exception if the value is invalid.
$enum_from_label = $enum_from->label();  // The request is underway

$enum_from = OrderStatus::tryFrom('processing'); // it is safe: get null instead of an exception
if ($enum_from) {
    $enum_from_label = $enum_from->label();  // The request is underway
}