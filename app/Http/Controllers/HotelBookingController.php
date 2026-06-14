<?php

namespace App\Http\Controllers;

use App\Exceptions\BookingActionException;
use App\Models\Booking;
use App\Models\Hotel;
use App\Enums\HotelWorkerAccess;
use App\Services\BookingService;
use App\Services\HotelAccessService;
use App\Services\HotelBookingService;
use Illuminate\Http\RedirectResponse;
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
        $this->hotelAccess->authorizeHotelCapability(auth()->user(), $hotel, HotelWorkerAccess::Bookings);

        $bookings = $this->hotelBookingService
            ->bookingsForHotel($hotel)
            ->each(fn (Booking $booking) => $this->bookingService->completeIfStayEnded($booking));

        $ownerLinks = $this->hotelAccess->ownerPanelLinks(auth()->user(), $hotel);

        return view('bookings.manage.index', compact('hotel', 'bookings', 'ownerLinks'));
    }

    public function show(Hotel $hotel, Booking $booking): View
    {
        $this->hotelAccess->authorizeHotelCapability(auth()->user(), $hotel, HotelWorkerAccess::Bookings);
        abort_unless($this->hotelBookingService->bookingBelongsToHotel($booking, $hotel), 404);

        $booking->load([
            'user',
            'room.hotel',
            'room.roomAmenities.hotelAmenity.amenity',
            'extraAmenities.hotelAmenity.amenity',
        ]);

        $this->bookingService->completeIfStayEnded($booking);

        $ownerLinks = $this->hotelAccess->ownerPanelLinks(auth()->user(), $hotel);

        return view('bookings.manage.show', compact('hotel', 'booking', 'ownerLinks'));
    }

    public function cancel(Hotel $hotel, Booking $booking): RedirectResponse
    {
        $this->hotelAccess->authorizeHotelCapability(auth()->user(), $hotel, HotelWorkerAccess::Bookings);
        abort_unless($this->hotelBookingService->bookingBelongsToHotel($booking, $hotel), 404);

        try {
            $this->bookingService->cancelBooking($booking);
        } catch (BookingActionException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('manage.hotels.bookings.index', $hotel)
            ->with('success', 'Rezerwacja została anulowana.');
    }
}
