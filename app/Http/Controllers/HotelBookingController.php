<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hotel;
use App\Services\BookingService;
use App\Services\HotelAccessService;
use App\Services\HotelBookingService;
use Illuminate\View\View;

class HotelBookingController extends Controller
{
    public function __construct(
        private readonly HotelAccessService $hotelAccess,
        private readonly HotelBookingService $hotelBookingService,
        private readonly BookingService $bookingService
    ) {
        $this->middleware('auth');
    }

    public function index(Hotel $hotel): View
    {
        $this->hotelAccess->authorizeHotelAccess(auth()->user(), $hotel);

        $bookings = $this->hotelBookingService
            ->bookingsForHotel($hotel)
            ->each(fn (Booking $booking) => $this->bookingService->completeIfStayEnded($booking));

        return view('bookings.manage.index', compact('hotel', 'bookings'));
    }

    public function show(Hotel $hotel, Booking $booking): View
    {
        $this->hotelAccess->authorizeHotelAccess(auth()->user(), $hotel);
        abort_unless($this->hotelBookingService->bookingBelongsToHotel($booking, $hotel), 404);

        $booking->load([
            'user',
            'room.hotel',
            'extraAmenities.hotelAmenity.amenity',
        ]);

        $this->bookingService->completeIfStayEnded($booking);

        return view('bookings.manage.show', compact('hotel', 'booking'));
    }
}
