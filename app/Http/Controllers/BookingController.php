<?php

namespace App\Http\Controllers;

use App\Exceptions\BookingActionException;
use App\Exceptions\RoomNotAvailableException;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $bookings = auth()->user()
            ->bookings()
            ->with(['room.hotel', 'extraAmenities.hotelAmenity.amenity'])
            ->latest()
            ->get()
            ->each(fn (Booking $booking) => $this->bookingService->completeIfStayEnded($booking));

        return view('bookings.index', compact('bookings'));
    }

    public function create(Hotel $hotel, Room $room): View|RedirectResponse
    {
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        $room->load([
            'hotel',
            'roomAmenities.hotelAmenity.amenity',
        ]);

        $paidAmenities = $room->roomAmenities->filter(
            fn ($roomAmenity) => (float) $roomAmenity->price > 0
                && $roomAmenity->hotelAmenity?->amenity !== null
        );

        return view('bookings.create', compact('hotel', 'room', 'paidAmenities'));
    }

    public function store(StoreBookingRequest $request, Hotel $hotel, Room $room): RedirectResponse
    {
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        try {
            $booking = $this->bookingService->createBooking(
                $request->user(),
                $room,
                Carbon::parse($request->validated('check_in'))->startOfDay(),
                Carbon::parse($request->validated('check_out'))->startOfDay(),
                $request->validated('extra_amenities', [])
            );
        } catch (RoomNotAvailableException $exception) {
            return back()
                ->withInput()
                ->withErrors(['check_in' => $exception->getMessage()]);
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['extra_amenities' => $exception->getMessage()]);
        }

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Rezerwacja została utworzona. Oczekuje na wpłatę.');
    }

    public function show(Booking $booking): View
    {
        $this->authorizeBooking($booking);

        $booking->load([
            'room.hotel',
            'room.roomAmenities.hotelAmenity.amenity',
            'extraAmenities.hotelAmenity.amenity',
        ]);

        $this->bookingService->completeIfStayEnded($booking);

        return view('bookings.show', compact('booking'));
    }

    public function pay(Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($booking);

        try {
            $this->bookingService->simulatePayment($booking);
        } catch (BookingActionException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Płatność została zaksięgowana.');
    }

    public function failPayment(Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($booking);

        try {
            $this->bookingService->simulatePaymentFailure($booking);
        } catch (BookingActionException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('error', 'Symulacja: płatność nie powiodła się.');
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($booking);

        try {
            $this->bookingService->cancelBooking($booking);
        } catch (BookingActionException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Rezerwacja została anulowana.');
    }

    private function authorizeBooking(Booking $booking): void
    {
        abort_unless($booking->user_id === auth()->id(), 403);
    }
}
