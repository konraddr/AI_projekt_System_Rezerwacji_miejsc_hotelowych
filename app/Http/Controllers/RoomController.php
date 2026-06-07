<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Hotel;
use App\Models\Room;
use App\Services\AmenityInheritanceService;
use App\Services\HotelAccessService;

class RoomController extends Controller
{
    public function __construct(
        private readonly AmenityInheritanceService $amenityService,
        private readonly HotelAccessService $hotelAccess
    ) {}

    public function manage(Hotel $hotel)
    {
        $this->hotelAccess->authorizeHotelAccess(auth()->user(), $hotel);

        $hotel->load([
            'rooms.roomAmenities.hotelAmenity.amenity',
        ]);

        return view('rooms.manage.index', compact('hotel'));
    }

    public function create(Hotel $hotel)
    {
        $this->hotelAccess->authorizeHotelAccess(auth()->user(), $hotel);

        $amenities = $hotel->amenities()->orderBy('name')->get();

        return view('rooms.create', compact('hotel', 'amenities'));
    }

    public function store(StoreRoomRequest $request, Hotel $hotel)
    {
        $this->hotelAccess->authorizeHotelAccess($request->user(), $hotel);

        $validated = $request->validated();

        $room = $hotel->rooms()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'capacity' => $validated['capacity'],
            'price_per_night' => $validated['price_per_night'],
            'quantity' => $validated['quantity'],
        ]);

        $amenityPrices = AmenityInheritanceService::parseAmenityPrices(
            $validated['amenities'] ?? null,
            $request->input('amenity_prices', [])
        );

        if ($amenityPrices !== []) {
            $this->amenityService->syncRoomAmenities($room, $amenityPrices);
        }

        return redirect()
            ->route('manage.rooms.index', $hotel)
            ->with('success', 'Pokój został dodany.');
    }

    public function edit(Hotel $hotel, Room $room)
    {
        $this->hotelAccess->authorizeHotelAccess(auth()->user(), $hotel);
        $this->ensureRoomBelongsToHotel($hotel, $room);

        $room->load('roomAmenities.hotelAmenity');
        $amenities = $hotel->amenities()->orderBy('name')->get();

        return view('rooms.edit', compact('hotel', 'room', 'amenities'));
    }

    public function update(UpdateRoomRequest $request, Hotel $hotel, Room $room)
    {
        $this->hotelAccess->authorizeHotelAccess($request->user(), $hotel);
        $this->ensureRoomBelongsToHotel($hotel, $room);

        $validated = $request->validated();

        $room->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'capacity' => $validated['capacity'],
            'price_per_night' => $validated['price_per_night'],
            'quantity' => $validated['quantity'],
        ]);

        $amenityPrices = AmenityInheritanceService::parseAmenityPrices(
            $validated['amenities'] ?? null,
            $request->input('amenity_prices', [])
        );

        $this->amenityService->syncRoomAmenities($room, $amenityPrices);

        return redirect()
            ->route('manage.rooms.index', $hotel)
            ->with('success', 'Pokój został zaktualizowany.');
    }

    public function destroy(Hotel $hotel, Room $room)
    {
        $this->hotelAccess->authorizeHotelAccess(auth()->user(), $hotel);
        $this->ensureRoomBelongsToHotel($hotel, $room);

        $room->delete();

        return redirect()
            ->route('manage.rooms.index', $hotel)
            ->with('success', 'Pokój został usunięty.');
    }

    private function ensureRoomBelongsToHotel(Hotel $hotel, Room $room): void
    {
        abort_if($room->hotel_id !== $hotel->id, 404);
    }
}
