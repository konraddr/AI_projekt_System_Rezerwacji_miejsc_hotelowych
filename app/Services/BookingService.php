<?php

namespace App\Services;

use App\Enums\BookingNotificationEvent;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\BookingActionException;
use App\Exceptions\RoomNotAvailableException;
use App\Models\Booking;
use App\Models\ExtraAmenity;
use App\Models\Room;
use App\Models\RoomAmenity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class BookingService
{
    public function __construct(
        private readonly BookingNotificationService $notificationService
    ) {}

    public function isRoomAvailable(Room $room, Carbon $checkIn, Carbon $checkOut): bool
    {
        $overlappingCount = Booking::query()
            ->where('room_id', $room->id)
            ->where('status', BookingStatus::Active)
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->count();

        return $overlappingCount < $room->quantity;
    }

    /**
     * @param  array<int>  $roomAmenityIds
     */
    public function calculateTotalPrice(
        Room $room,
        Carbon $checkIn,
        Carbon $checkOut,
        array $roomAmenityIds = []
    ): float {
        $nights = $checkIn->diffInDays($checkOut);
        $roomTotal = (float) $room->price_per_night * $nights;

        $extrasTotal = $this->resolveExtraAmenities($room, $roomAmenityIds)
            ->sum(fn (RoomAmenity $roomAmenity): float => (float) $roomAmenity->price);

        return round($roomTotal + $extrasTotal, 2);
    }

    /**
     * @param  array<int>  $roomAmenityIds
     */
    public function createBooking(
        User $user,
        Room $room,
        Carbon $checkIn,
        Carbon $checkOut,
        array $roomAmenityIds = []
    ): Booking {
        if ($checkOut->lte($checkIn)) {
            throw new InvalidArgumentException('Data wyjazdu musi być późniejsza niż data przyjazdu.');
        }

        if (! $this->isRoomAvailable($room, $checkIn, $checkOut)) {
            throw new RoomNotAvailableException;
        }

        $extraAmenities = $this->resolveExtraAmenities($room, $roomAmenityIds);
        $totalPrice = $this->calculateTotalPrice($room, $checkIn, $checkOut, $roomAmenityIds);

        return DB::transaction(function () use ($user, $room, $checkIn, $checkOut, $extraAmenities, $totalPrice): Booking {
            $booking = Booking::create([
                'user_id' => $user->id,
                'room_id' => $room->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'total_price' => $totalPrice,
                'payment_status' => PaymentStatus::Pending,
                'status' => BookingStatus::Active,
            ]);

            foreach ($extraAmenities as $roomAmenity) {
                ExtraAmenity::create([
                    'booking_id' => $booking->id,
                    'hotel_amenity_id' => $roomAmenity->hotel_amenity_id,
                    'price' => $roomAmenity->price,
                ]);
            }

            $booking->load([
                'room.hotel',
                'extraAmenities.hotelAmenity.amenity',
            ]);

            $this->notificationService->notify($booking, BookingNotificationEvent::Created);

            return $booking;
        });
    }

    public function completeIfStayEnded(Booking $booking): Booking
    {
        if (
            $booking->status === BookingStatus::Active
            && $booking->payment_status === PaymentStatus::Paid
            && $booking->check_out->lte(now()->startOfDay())
        ) {
            $booking->update(['status' => BookingStatus::Completed]);
            $booking->refresh();
            $booking->loadMissing(['room.hotel']);
            $this->notificationService->notify($booking, BookingNotificationEvent::Completed);
        }

        return $booking;
    }

    public function simulatePayment(Booking $booking): Booking
    {
        if (! $booking->canPay()) {
            throw new BookingActionException('Ta rezerwacja nie oczekuje już na wpłatę.');
        }

        $booking->update(['payment_status' => PaymentStatus::Paid]);
        $booking->refresh();
        $booking->loadMissing(['room.hotel']);
        $this->notificationService->notify($booking, BookingNotificationEvent::PaymentPaid);

        return $booking;
    }

    public function simulatePaymentFailure(Booking $booking): Booking
    {
        if (! $booking->canPay()) {
            throw new BookingActionException('Nie można oznaczyć tej rezerwacji jako nieopłaconej.');
        }

        $booking->update(['payment_status' => PaymentStatus::Failed]);
        $booking->refresh();
        $booking->loadMissing(['room.hotel']);
        $this->notificationService->notify($booking, BookingNotificationEvent::PaymentFailed);

        return $booking;
    }

    public function cancelBooking(Booking $booking): Booking
    {
        if (! $booking->canCancel()) {
            throw new BookingActionException('Tej rezerwacji nie można już anulować.');
        }

        $booking->update(['status' => BookingStatus::Cancelled]);
        $booking->refresh();
        $booking->loadMissing(['room.hotel']);
        $this->notificationService->notify($booking, BookingNotificationEvent::Cancelled);

        return $booking;
    }

    /**
     * @param  array<int>  $roomAmenityIds
     * @return Collection<int, RoomAmenity>
     */
    private function resolveExtraAmenities(Room $room, array $roomAmenityIds): Collection
    {
        if ($roomAmenityIds === []) {
            return RoomAmenity::query()->whereKey([])->get();
        }

        $amenities = RoomAmenity::query()
            ->where('room_id', $room->id)
            ->whereIn('id', $roomAmenityIds)
            ->where('price', '>', 0)
            ->get();

        if ($amenities->count() !== count(array_unique($roomAmenityIds))) {
            throw new InvalidArgumentException('Wybrane udogodnienia są nieprawidłowe dla tego pokoju.');
        }

        return $amenities;
    }
}
