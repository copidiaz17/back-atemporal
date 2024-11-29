<?php

declare(strict_types=1);

namespace App\Enums;

enum UserType: string
{
    case Admin = 'admin';
    case Client = 'client';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
