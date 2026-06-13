<?php

namespace App\Services;

use App\Enums\BookingNotificationEvent;
use App\Models\Booking;
use App\Notifications\BookingStatusNotification;
use Throwable;

class BookingNotificationService
{
    public function notify(Booking $booking, BookingNotificationEvent $event): void
    {
        $booking->loadMissing(['room.hotel', 'user']);

        if ($booking->user === null) {
            return;
        }

        try {
            $booking->user->notify(new BookingStatusNotification($booking, $event));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
