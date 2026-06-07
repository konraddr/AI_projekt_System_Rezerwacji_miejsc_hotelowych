<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Collection;

class HotelBookingService
{
    /**
     * @return Collection<int, Booking>
     */
    public function bookingsForHotel(Hotel $hotel): Collection
    {
        return Booking::query()
            ->with(['user', 'room', 'extraAmenities.hotelAmenity.amenity'])
            ->whereHas('room', fn ($query) => $query->where('hotel_id', $hotel->id))
            ->latest()
            ->get();
    }

    public function bookingBelongsToHotel(Booking $booking, Hotel $hotel): bool
    {
        $booking->loadMissing('room');

        return $booking->room !== null && $booking->room->hotel_id === $hotel->id;
    }
}
