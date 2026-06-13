<?php

namespace App\Enums;

use App\Models\Booking;

enum BookingNotificationEvent: string
{
    case Created = 'created';
    case PaymentPaid = 'payment_paid';
    case PaymentFailed = 'payment_failed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case Penalized = 'penalized';

    public function title(): string
    {
        return match ($this) {
            self::Created => 'Nowa rezerwacja',
            self::PaymentPaid => 'Płatność potwierdzona',
            self::PaymentFailed => 'Płatność nieudana',
            self::Cancelled => 'Rezerwacja anulowana',
            self::Completed => 'Pobyt zakończony',
            self::Penalized => 'Konto zablokowane',
        };
    }

    public function body(Booking $booking): string
    {
        $hotel = $booking->room->hotel->name;
        $room = $booking->room->name;

        return match ($this) {
            self::Created => "Utworzono rezerwację #{$booking->id} w {$hotel} ({$room}). Oczekuje na wpłatę.",
            self::PaymentPaid => "Rezerwacja #{$booking->id} w {$hotel} została opłacona.",
            self::PaymentFailed => "Płatność za rezerwację #{$booking->id} w {$hotel} nie powiodła się.",
            self::Cancelled => "Rezerwacja #{$booking->id} w {$hotel} została anulowana.",
            self::Completed => "Pobyt w {$hotel} ({$room}) z rezerwacji #{$booking->id} został zakończony.",
            self::Penalized => "Rezerwacja #{$booking->id} w {$hotel} anulowana z powodu braku wpłaty. Twoje konto zostało zablokowane.",
        };
    }
}
