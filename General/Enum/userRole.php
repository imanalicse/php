<?php

namespace App\General\Enum;
enum UserRole: int
{
    case SuperAdmin = 1;
    case Admin = 2;
    case Customer = 3;
}

// var_dump(UserRole::Admin); // enum(UserRole::Admin)
// var_dump(UserRole::tryFrom('admin')); // enum(UserRole::Admin)

// UserRole::Admin->name - Admin
// UserRole::Admin->value - 2

var_dump(UserRole::from(1)->name);