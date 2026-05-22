<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Services\AmenityInheritanceService;

class HotelController extends Controller
{
    public function __construct(
        private readonly AmenityInheritanceService $amenityService
    ) {}

    public function index()
    {
        $hotels = Hotel::with('amenities')->latest()->paginate(12);

        return view('hotels.index', compact('hotels'));
    }

    public function show(Hotel $hotel)
    {
        $hotel->load([
            'rooms.roomAmenities.hotelAmenity.amenity',
            'amenities',
        ]);

        return view('hotels.show', compact('hotel'));
    }

    public function manage()
    {
        $hotels = Hotel::withCount(['rooms', 'amenities'])->latest()->get();

        return view('hotels.manage.index', compact('hotels'));
    }

    public function create()
    {
        $amenities = Amenity::orderBy('name')->get();

        return view('hotels.create', compact('amenities'));
    }

    public function store(StoreHotelRequest $request)
    {
        $validated = $request->validated();

        $hotel = Hotel::create([
            'name' => $validated['name'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'description' => $validated['description'],
            'latitude' => $request->input('latitude', 52.069),
            'longitude' => $request->input('longitude', 19.480),
        ]);

        $amenityPrices = AmenityInheritanceService::parseAmenityPrices(
            $validated['amenities'] ?? null,
            $request->input('amenity_prices', [])
        );

        if ($amenityPrices !== []) {
            $this->amenityService->syncHotelAmenities($hotel, $amenityPrices);
        }

        return redirect()
            ->route('manage.hotels.index')
            ->with('success', 'Hotel został dodany.');
    }

    public function edit(Hotel $hotel)
    {
        $hotel->load('amenities');
        $amenities = Amenity::orderBy('name')->get();

        return view('hotels.edit', compact('hotel', 'amenities'));
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel)
    {
        $validated = $request->validated();

        $hotel->update([
            'name' => $validated['name'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'description' => $validated['description'],
            'latitude' => $request->input('latitude', $hotel->latitude),
            'longitude' => $request->input('longitude', $hotel->longitude),
        ]);

        $amenityPrices = AmenityInheritanceService::parseAmenityPrices(
            $validated['amenities'] ?? null,
            $request->input('amenity_prices', [])
        );

        $this->amenityService->syncHotelAmenities($hotel, $amenityPrices);

        return redirect()
            ->route('manage.hotels.index')
            ->with('success', 'Hotel został zaktualizowany.');
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();

        return redirect()
            ->route('manage.hotels.index')
            ->with('success', 'Hotel został usunięty.');
    }
}
