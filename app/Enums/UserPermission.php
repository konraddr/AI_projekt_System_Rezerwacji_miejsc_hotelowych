<?php

namespace App\Enums;

enum UserPermission: int
{
    case Administrator = 0;
    case Owner = 1;
    case Worker = 2;
    case Client = 5;
    case Banned = 6;

    public function label(): string
    {
        return match ($this) {
            self::Administrator => 'Administrator',
            self::Owner => 'Właściciel hotelu',
            self::Worker => 'Pracownik hotelu',
            self::Client => 'Klient',
            self::Banned => 'Konto zablokowane',
        };
    }

    public function isBanned(): bool
    {
        return $this === self::Banned;
    }
}
