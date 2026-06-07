<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ReviewEligibilityService
{
    /**
     * @return Collection<int, Booking>
     */
    public function eligibleBookingsForHotel(Hotel $hotel, User $user): Collection
    {
        return Booking::query()
            ->with('room')
            ->where('user_id', $user->id)
            ->where('payment_status', PaymentStatus::Paid)
            ->where('status', BookingStatus::Completed)
            ->whereHas('room', fn ($query) => $query->where('hotel_id', $hotel->id))
            ->whereDoesntHave('review')
            ->orderByDesc('check_out')
            ->get();
    }

    public function isEligible(Booking $booking, Hotel $hotel, User $user): bool
    {
        if ($booking->user_id !== $user->id) {
            return false;
        }

        if (! $booking->qualifiesForReview()) {
            return false;
        }

        $booking->loadMissing('room');

        if ($booking->room === null || $booking->room->hotel_id !== $hotel->id) {
            return false;
        }

        return ! $booking->review()->exists();
    }
}
