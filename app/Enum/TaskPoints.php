<?php

declare(strict_types=1);

namespace App\Enum;

enum TaskPoints: int
{
    case ONE = 1;
    case THREE = 3;
    case FIVE = 5;
    case EIGHT = 8;
    case THIRTEEN = 13;
    case TWENTY_ONE = 21;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
