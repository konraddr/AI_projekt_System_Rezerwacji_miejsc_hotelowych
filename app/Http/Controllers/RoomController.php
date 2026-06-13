<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Enums\HotelWorkerAccess;
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
        $this->hotelAccess->authorizeHotelCapability(auth()->user(), $hotel, HotelWorkerAccess::Rooms);

        $hotel->load([
            'rooms.roomAmenities.hotelAmenity.amenity',
        ]);

        return view('rooms.manage.index', compact('hotel'));
    }

    public function create(Hotel $hotel)
    {
        $this->hotelAccess->authorizeHotelCapability(auth()->user(), $hotel, HotelWorkerAccess::Rooms);

        $amenities = $hotel->amenities()->orderBy('name')->get();

        return view('rooms.create', compact('hotel', 'amenities'));
    }

    public function store(StoreRoomRequest $request, Hotel $hotel)
    {
        $this->hotelAccess->authorizeHotelCapability($request->user(), $hotel, HotelWorkerAccess::Rooms);

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
        $this->hotelAccess->authorizeHotelCapability(auth()->user(), $hotel, HotelWorkerAccess::Rooms);
        $this->ensureRoomBelongsToHotel($hotel, $room);

        $room->load('roomAmenities.hotelAmenity');
        $amenities = $hotel->amenities()->orderBy('name')->get();

        $selectedAmenities = old('amenities', $room->roomAmenities
            ->map(fn ($item) => $item->hotelAmenity?->amenity_id)
            ->filter()
            ->all());
        $amenityPrices = old('amenity_prices', $room->roomAmenities
            ->filter(fn ($item) => $item->hotelAmenity?->amenity_id)
            ->mapWithKeys(fn ($item) => [
                $item->hotelAmenity->amenity_id => $item->price,
            ])->all());

        return view('rooms.edit', compact('hotel', 'room', 'amenities', 'selectedAmenities', 'amenityPrices'));
    }

    public function update(UpdateRoomRequest $request, Hotel $hotel, Room $room)
    {
        $this->hotelAccess->authorizeHotelCapability($request->user(), $hotel, HotelWorkerAccess::Rooms);
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
        $this->hotelAccess->authorizeHotelCapability(auth()->user(), $hotel, HotelWorkerAccess::Rooms);
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
