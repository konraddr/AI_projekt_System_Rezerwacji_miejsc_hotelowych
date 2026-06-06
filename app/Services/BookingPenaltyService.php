<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserPermission;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingPenaltyService
{
    public function penalizeOverdueUnpaidBookings(): int
    {
        $deadline = now()->subHours(config('bookings.unpaid_grace_hours'));

        $bookings = Booking::query()
            ->with('user')
            ->where('payment_status', PaymentStatus::Pending)
            ->where('status', BookingStatus::Active)
            ->where('created_at', '<=', $deadline)
            ->get();

        $penalizedCount = 0;

        foreach ($bookings as $booking) {
            DB::transaction(function () use ($booking): void {
                $booking->update(['status' => BookingStatus::Cancelled]);

                $user = $booking->user;

                if (
                    $user !== null
                    && $user->permission === UserPermission::Client
                ) {
                    $user->update(['permission' => UserPermission::Banned]);
                }
            });

            $penalizedCount++;
        }

        return $penalizedCount;
    }
}
