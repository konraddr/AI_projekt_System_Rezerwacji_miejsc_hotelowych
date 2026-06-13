<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\HotelWorkerAccess;
use App\Enums\UserPermission;
use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Services\AmenityInheritanceService;
use App\Services\HotelAccessService;
use App\Services\HotelAvailabilityService;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function __construct(
        private readonly AmenityInheritanceService $amenityService,
        private readonly HotelAccessService $hotelAccess,
        private readonly HotelAvailabilityService $availability
    ) {}

    public function index(Request $request)
    {
        $stayDates = $this->availability->parseStayDates(
            $request->query('check_in'),
            $request->query('check_out')
        );
        $guests = $request->filled('guests') ? max(1, $request->integer('guests')) : null;

        $hotelsQuery = Hotel::query()->with('amenities');

        if ($stayDates !== null) {
            $hotelsQuery = $this->availability->applyAvailableHotelsScope(
                $hotelsQuery,
                $stayDates['check_in'],
                $stayDates['check_out'],
                $guests
            );
        }

        $hotels = $hotelsQuery
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = '%'.$request->string('q').'%';
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'ilike', $search)
                        ->orWhere('city', 'ilike', $search)
                        ->orWhere('address', 'ilike', $search);
                });
            })
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->string('city')))
            ->when($request->sort === 'name_asc', fn ($query) => $query->orderBy('name'))
            ->when($request->sort === 'name_desc', fn ($query) => $query->orderByDesc('name'))
            ->when($request->sort === 'city_asc', fn ($query) => $query->orderBy('city'))
            ->when($request->sort === 'city_desc', fn ($query) => $query->orderByDesc('city'))
            ->when(! $request->filled('sort'), fn ($query) => $query->latest())
            ->paginate(12)
            ->withQueryString();

        $cities = Hotel::query()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        $staySearch = [
            'check_in' => $request->query('check_in'),
            'check_out' => $request->query('check_out'),
            'guests' => $guests,
            'active' => $stayDates !== null,
        ];

        return view('hotels.index', compact('hotels', 'cities', 'staySearch'));
    }

    public function show(Request $request, Hotel $hotel)
    {
        $roomSort = $request->query('room_sort', 'price_asc');
        $stayDates = $this->availability->parseStayDates(
            $request->query('check_in'),
            $request->query('check_out')
        );
        $guests = $request->filled('guests') ? max(1, $request->integer('guests')) : null;

        $roomsQuery = $hotel->rooms()->with([
            'roomAmenities.hotelAmenity.amenity',
            'photos',
        ]);

        if ($stayDates !== null) {
            if ($guests !== null) {
                $roomsQuery->where('capacity', '>=', $guests);
            }

            $roomsQuery->whereRaw(
                '(select count(*) from bookings where bookings.room_id = rooms.id and bookings.status = ? and bookings.check_in < ? and bookings.check_out > ?) < rooms.quantity',
                [
                    BookingStatus::Active->value,
                    $stayDates['check_out']->toDateString(),
                    $stayDates['check_in']->toDateString(),
                ]
            );
        }

        match ($roomSort) {
            'price_desc' => $roomsQuery->orderByDesc('price_per_night'),
            'name_asc' => $roomsQuery->orderBy('name'),
            'name_desc' => $roomsQuery->orderByDesc('name'),
            'capacity_asc' => $roomsQuery->orderBy('capacity'),
            'capacity_desc' => $roomsQuery->orderByDesc('capacity'),
            default => $roomsQuery->orderBy('price_per_night'),
        };

        $hotel->load(['amenities', 'photos']);

        $rooms = $roomsQuery
            ->paginate(5)
            ->withQueryString();

        $hotelReviews = $hotel->reviews()->visible()->with('user')->latest()->get();
        $userReview = $request->user()
            ? $hotel->reviews()->where('user_id', $request->user()->id)->first()
            : null;

        $staySearch = [
            'check_in' => $request->query('check_in'),
            'check_out' => $request->query('check_out'),
            'guests' => $guests,
            'active' => $stayDates !== null,
        ];

        $stayQuery = array_filter([
            'check_in' => $staySearch['check_in'],
            'check_out' => $staySearch['check_out'],
            'guests' => $staySearch['guests'],
        ], fn ($value) => $value !== null && $value !== '');

        return view('hotels.show', compact(
            'hotel',
            'rooms',
            'roomSort',
            'hotelReviews',
            'userReview',
            'staySearch',
            'stayQuery',
        ));
    }

    public function manage()
    {
        $user = auth()->user();
        $hotels = $this->hotelAccess->hotelsForUser($user);

        $ownerLinksByHotel = $hotels->mapWithKeys(
            fn (Hotel $hotel) => [$hotel->id => $this->hotelAccess->ownerPanelLinks($user, $hotel)]
        );

        return view('hotels.manage.index', compact('hotels', 'ownerLinksByHotel'));
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
            'owner_id' => $request->user()->id,
            'name' => $validated['name'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'description' => $validated['description'],
            'latitude' => $request->input('latitude', 52.069),
            'longitude' => $request->input('longitude', 19.480),
        ]);

        $hotel->workers()->attach($request->user()->id, [
            'permissions' => HotelWorkerAccess::values(),
        ]);

        if ($request->user()->hasPermission(UserPermission::Client)) {
            $request->user()->update(['permission' => UserPermission::Owner]);
        }

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
        $this->hotelAccess->authorizeHotelCapability(auth()->user(), $hotel, HotelWorkerAccess::Hotel);

        $hotel->load('amenities');
        $amenities = Amenity::orderBy('name')->get();

        $selectedAmenities = old('amenities', $hotel->amenities->pluck('id')->all());
        $amenityPrices = old('amenity_prices', $hotel->amenities
            ->mapWithKeys(fn ($amenity) => [$amenity->id => $amenity->pivot->price])
            ->all());

        return view('hotels.edit', compact('hotel', 'amenities', 'selectedAmenities', 'amenityPrices'));
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel)
    {
        $this->hotelAccess->authorizeHotelCapability($request->user(), $hotel, HotelWorkerAccess::Hotel);

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
        $this->hotelAccess->authorizeHotelCapability(auth()->user(), $hotel, HotelWorkerAccess::Hotel);

        $hotel->delete();

        return redirect()
            ->route('manage.hotels.index')
            ->with('success', 'Hotel został usunięty.');
    }
}
