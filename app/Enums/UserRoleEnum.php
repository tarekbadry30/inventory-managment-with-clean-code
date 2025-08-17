<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case ACCOUNTANT = 'accountant';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::USER => 'User',
            self::ACCOUNTANT => 'Accountant',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
