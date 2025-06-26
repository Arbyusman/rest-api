<?php

namespace App\Enums;

enum Statuses: int
{
    case ToDo = 1;
    case Doing = 2;
    case Done = 3;
    case Canceled = 4;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
