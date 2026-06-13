<?php

namespace App\Http\Controllers;

use App\Enums\HotelWorkerAccess;
use App\Enums\UserPermission;
use App\Http\Requests\AdminStoreHotelRequest;
use App\Http\Requests\AdminUpdateHotelRequest;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\User;
use App\Services\AmenityInheritanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminHotelController extends Controller
{
    public function __construct(
        private readonly AmenityInheritanceService $amenityService
    ) {}

    public function index(): View
    {
        $hotels = Hotel::query()
            ->with('owner')
            ->withCount(['rooms', 'amenities'])
            ->latest()
            ->paginate(20);

        return view('admin.hotels.index', compact('hotels'));
    }

    public function create(): View
    {
        $amenities = Amenity::orderBy('name')->get();
        $owners = User::query()
            ->whereIn('permission', [UserPermission::Administrator, UserPermission::Owner])
            ->orderBy('name')
            ->get();

        return view('admin.hotels.create', compact('amenities', 'owners'));
    }

    public function store(AdminStoreHotelRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $owner = User::query()->findOrFail($validated['owner_id']);

        $hotel = Hotel::create([
            'owner_id' => $owner->id,
            'name' => $validated['name'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'description' => $validated['description'],
            'latitude' => $request->input('latitude', 52.069),
            'longitude' => $request->input('longitude', 19.480),
        ]);

        $hotel->workers()->syncWithoutDetaching([
            $owner->id => ['permissions' => HotelWorkerAccess::values()],
        ]);

        $amenityPrices = AmenityInheritanceService::parseAmenityPrices(
            $validated['amenities'] ?? null,
            $request->input('amenity_prices', [])
        );

        if ($amenityPrices !== []) {
            $this->amenityService->syncHotelAmenities($hotel, $amenityPrices);
        }

        return redirect()
            ->route('manage.admin.hotels.show', $hotel)
            ->with('success', 'Hotel został utworzony.');
    }

    public function show(Hotel $hotel): View
    {
        $hotel->load(['owner', 'amenities'])
            ->loadCount(['rooms', 'reviews', 'photos']);

        return view('admin.hotels.show', compact('hotel'));
    }

    public function edit(Hotel $hotel): View
    {
        $hotel->load('amenities');
        $amenities = Amenity::orderBy('name')->get();
        $owners = User::query()
            ->whereIn('permission', [UserPermission::Administrator, UserPermission::Owner])
            ->orderBy('name')
            ->get();

        return view('admin.hotels.edit', compact('hotel', 'amenities', 'owners'));
    }

    public function update(AdminUpdateHotelRequest $request, Hotel $hotel): RedirectResponse
    {
        $validated = $request->validated();
        $ownerId = (int) $validated['owner_id'];

        $hotel->update([
            'owner_id' => $ownerId,
            'name' => $validated['name'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'description' => $validated['description'],
            'latitude' => $request->input('latitude', $hotel->latitude),
            'longitude' => $request->input('longitude', $hotel->longitude),
        ]);

        $hotel->workers()->syncWithoutDetaching([
            $ownerId => ['permissions' => HotelWorkerAccess::values()],
        ]);

        $amenityPrices = AmenityInheritanceService::parseAmenityPrices(
            $validated['amenities'] ?? null,
            $request->input('amenity_prices', [])
        );

        $this->amenityService->syncHotelAmenities($hotel, $amenityPrices);

        return redirect()
            ->route('manage.admin.hotels.show', $hotel)
            ->with('success', 'Hotel został zaktualizowany.');
    }

    public function destroy(Hotel $hotel): RedirectResponse
    {
        $name = $hotel->name;
        $hotel->delete();

        return redirect()
            ->route('manage.admin.hotels.index')
            ->with('success', 'Hotel „'.$name.'” został usunięty.');
    }
}
