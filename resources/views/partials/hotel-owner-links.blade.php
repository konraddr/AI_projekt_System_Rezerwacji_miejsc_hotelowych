@php
    $hotelAccess = app(\App\Services\HotelAccessService::class);
    $currentUser = auth()->user();
@endphp

@if ($hotelAccess->userCanAccess($currentUser, $hotel, \App\Enums\HotelWorkerAccess::Bookings))
    <a href="{{ route('manage.hotels.bookings.index', $hotel) }}" class="btn btn-sm btn-outline-info">
        Rezerwacje
    </a>
@endif
@if ($hotelAccess->userCanAccess($currentUser, $hotel, \App\Enums\HotelWorkerAccess::Workers))
    <a href="{{ route('manage.hotels.workers.index', $hotel) }}" class="btn btn-sm btn-outline-secondary">
        Pracownicy
    </a>
@endif
@if ($hotelAccess->userCanAccess($currentUser, $hotel, \App\Enums\HotelWorkerAccess::Rooms))
    <a href="{{ route('manage.rooms.index', $hotel) }}" class="btn btn-sm btn-outline-primary">
        Pokoje
    </a>
@endif
