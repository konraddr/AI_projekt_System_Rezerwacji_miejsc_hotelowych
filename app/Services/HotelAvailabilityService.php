<?php

namespace App\Services;

use App\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class HotelAvailabilityService
{
    public function hasValidStayDates(?string $checkIn, ?string $checkOut): bool
    {
        if ($checkIn === null || $checkOut === null || $checkIn === '' || $checkOut === '') {
            return false;
        }

        try {
            $checkInDate = Carbon::parse($checkIn)->startOfDay();
            $checkOutDate = Carbon::parse($checkOut)->startOfDay();
        } catch (\Throwable) {
            return false;
        }

        return $checkOutDate->gt($checkInDate)
            && $checkInDate->gte(now()->startOfDay());
    }

    /**
     * @return array{check_in: Carbon, check_out: Carbon}|null
     */
    public function parseStayDates(?string $checkIn, ?string $checkOut): ?array
    {
        if (! $this->hasValidStayDates($checkIn, $checkOut)) {
            return null;
        }

        return [
            'check_in' => Carbon::parse($checkIn)->startOfDay(),
            'check_out' => Carbon::parse($checkOut)->startOfDay(),
        ];
    }

    public function applyAvailableHotelsScope(
        Builder $query,
        Carbon $checkIn,
        Carbon $checkOut,
        ?int $guests = null
    ): Builder {
        return $query->whereHas('rooms', function (Builder $roomsQuery) use ($checkIn, $checkOut, $guests): void {
            if ($guests !== null && $guests > 0) {
                $roomsQuery->where('capacity', '>=', $guests);
            }

            $roomsQuery->whereRaw(
                '(select count(*) from bookings where bookings.room_id = rooms.id and bookings.status = ? and bookings.check_in < ? and bookings.check_out > ?) < rooms.quantity',
                [BookingStatus::Active->value, $checkOut->toDateString(), $checkIn->toDateString()]
            );
        });
    }
}
