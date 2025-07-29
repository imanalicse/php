<?php

namespace App\General\Enum;

enum OrderStatus : string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function label(): string {
        return match($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
        };
    }

    // get all scalar values as array
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

$label = OrderStatus::PROCESSING->label(); // Processing
$value = OrderStatus::PROCESSING->value; // processing
$name = OrderStatus::PROCESSING->name; // PROCESSING
echo '<pre>';
print_r(OrderStatus::values());
echo '</pre>';