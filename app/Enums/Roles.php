<?php

namespace App\Enums;

enum Roles: int
{
    case Manager = 1;
    case Staff = 2;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
