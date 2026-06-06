<?php

namespace App\Services;

use App\Enums\BookingNotificationEvent;
use App\Models\Booking;
use App\Notifications\BookingStatusNotification;

class BookingNotificationService
{
    public function notify(Booking $booking, BookingNotificationEvent $event): void
    {
        $booking->loadMissing(['room.hotel', 'user']);

        if ($booking->user === null) {
            return;
        }

        $booking->user->notify(new BookingStatusNotification($booking, $event));
    }
}
