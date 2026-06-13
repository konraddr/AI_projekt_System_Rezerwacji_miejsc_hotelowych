<?php

namespace App\Enums;

enum HotelWorkerAccess: string
{
    case Hotel = 'hotel';
    case Rooms = 'rooms';
    case Bookings = 'bookings';
    case Workers = 'workers';
    case Photos = 'photos';
    case Chat = 'chat';

    public function label(): string
    {
        return match ($this) {
            self::Hotel => 'Edycja hotelu',
            self::Rooms => 'Pokoje',
            self::Bookings => 'Rezerwacje',
            self::Workers => 'Pracownicy',
            self::Photos => 'Zdjęcia',
            self::Chat => 'Czat',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
