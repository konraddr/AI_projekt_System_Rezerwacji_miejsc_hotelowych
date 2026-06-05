<?php

namespace App\Http\Controllers;

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
            ->get();

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
        abort_unless($booking->user_id === auth()->id(), 403);

        $booking->load([
            'room.hotel',
            'extraAmenities.hotelAmenity.amenity',
        ]);

        return view('bookings.show', compact('booking'));
    }
}
