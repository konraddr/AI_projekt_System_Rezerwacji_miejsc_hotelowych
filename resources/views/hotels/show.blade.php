@extends('layouts.app')

@section('title', $hotel->name)

@section('content')
    <div class="container py-2">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hotels.index') }}">Katalog</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="mb-4">
                    <h1 class="display-6 fw-bold mb-1">{{ $hotel->name }}</h1>
                    <p class="text-muted fs-5 mb-0">{{ $hotel->city }}, {{ $hotel->address }}</p>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h2 class="h5 fw-bold border-bottom pb-2 mb-3">O obiekcie</h2>
                        <p class="mb-0" style="line-height: 1.8;">{{ $hotel->description }}</p>
                    </div>
                </div>

                @include('partials.hotel-public-photo-gallery')

                @include('partials.hotel-reviews-public')

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h2 class="h5 fw-bold border-bottom pb-2 mb-3">Udogodnienia obiektu</h2>
                        @forelse ($hotel->amenities as $amenity)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span>{{ $amenity->name }}</span>
                                @if ((float) $amenity->pivot->price > 0)
                                    <span class="badge bg-primary">{{ number_format($amenity->pivot->price, 2) }} PLN</span>
                                @else
                                    <span class="badge bg-success">Gratis</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted mb-0">Brak przypisanych udogodnień.</p>
                        @endforelse
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h2 class="h5 fw-bold border-bottom pb-2 mb-3">Lokalizacja</h2>
                        @if ($hotel->latitude && $hotel->longitude)
                            <div id="hotel-map-show"
                                 class="rounded border overflow-hidden"
                                 data-lat="{{ $hotel->latitude }}"
                                 data-lng="{{ $hotel->longitude }}"
                                 data-address="{{ $hotel->address }}, {{ $hotel->city }}">
                            </div>
                            <p class="text-muted small mt-2 mb-0">
                                Współrzędne: {{ $hotel->latitude }}, {{ $hotel->longitude }}
                            </p>
                        @else
                            <div class="alert alert-warning mb-0">Hotel nie ma jeszcze zapisanych współrzędnych.</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 border-top border-primary border-4">
                    <div class="card-header bg-white">
                        <h2 class="h5 fw-bold mb-0">Dostępne pokoje</h2>
                    </div>

                    @auth
                        <div class="px-3 pt-3">
                            <a href="{{ route('manage.rooms.create', $hotel) }}" class="btn btn-success w-100 btn-sm">
                                Dodaj pokój (panel)
                            </a>
                        </div>
                    @endauth

                    <ul class="list-group list-group-flush mt-2">
                        @forelse ($hotel->rooms as $room)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h3 class="h6 fw-bold mb-0">{{ $room->name }}</h3>
                                    <span class="badge bg-primary">{{ number_format($room->price_per_night, 2) }} PLN / noc</span>
                                </div>
                                <p class="small text-muted mb-2">{{ Str::limit($room->description, 80) }}</p>

                                @include('partials.room-public-photo-thumbs', ['room' => $room])

                                <p class="small mb-2">
                                    <span class="badge bg-light text-dark border">{{ $room->capacity }} os.</span>
                                    <span class="badge bg-light text-dark border">{{ $room->quantity }} szt.</span>
                                </p>

                                @if ($room->roomAmenities->isNotEmpty())
                                    <div class="small mb-3">
                                        <strong>Udogodnienia pokoju:</strong>
                                        <ul class="mb-0 ps-3">
                                            @foreach ($room->roomAmenities as $roomAmenity)
                                                @if ($roomAmenity->hotelAmenity?->amenity)
                                                    <li>
                                                        {{ $roomAmenity->hotelAmenity->amenity->name }} —
                                                        @if ((float) $roomAmenity->price > 0)
                                                            {{ number_format($roomAmenity->price, 2) }} PLN
                                                        @else
                                                            gratis
                                                        @endif
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @auth
                                    <a href="{{ route('bookings.create', [$hotel, $room]) }}"
                                       class="btn btn-outline-primary btn-sm w-100">
                                        Rezerwuj
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="btn btn-outline-primary btn-sm w-100">
                                        Zaloguj się, aby zarezerwować
                                    </a>
                                @endauth
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-4">
                                Brak dostępnych pokoi.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@if ($hotel->latitude && $hotel->longitude)
    @push('styles')
        @include('partials.leaflet-assets', ['includeJs' => false])
        <link rel="stylesheet" href="{{ asset('css/hotel-map.css') }}">
    @endpush

    @push('scripts')
        @include('partials.leaflet-assets', ['includeJs' => true])
        <script src="{{ asset('js/hotel-map-show.js') }}"></script>
    @endpush
@endif
