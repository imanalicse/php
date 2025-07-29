<?php

namespace App\General\Enum;
enum Status
{
    case DRAFT;
    case PUBLISHED;
    case ARCHIVED;

    public function color(): string
    {
        return match ($this) {
            Status::DRAFT => 'grey',
            Status::PUBLISHED => 'green',
            Status::ARCHIVED => 'red',
        };
    }
}

echo "<pre>";
print_r(Status::cases());
echo "</pre>";
echo "<pre>";
print_r(Status::PUBLISHED->name); // PUBLISHED
echo "</pre>";

echo "<pre>";
print_r(Status::PUBLISHED->color()); // green
echo "</pre>";