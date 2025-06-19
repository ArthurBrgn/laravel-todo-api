<?php

declare(strict_types=1);

namespace App\Enum;

enum TaskStatus: string
{
    case TODO = 'todo';
    case DOING = 'doing';
    case REVIEW = 'review';
    case BLOCKED = 'blocked';
    case DONE = 'done';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
