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
            {{-- Lewa kolumna: opis, udogodnienia, pokoje --}}
            <div class="col-lg-7">
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

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body py-3">
                        <h2 class="h6 fw-bold mb-2">Udogodnienia obiektu</h2>
                        @if ($hotel->amenities->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-sm table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Udogodnienie</th>
                                            <th class="text-end" style="width: 7rem;">Cena</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($hotel->amenities as $amenity)
                                            <tr>
                                                <td class="small">{{ $amenity->name }}</td>
                                                <td class="text-end small">
                                                    @if ((float) $amenity->pivot->price > 0)
                                                        {{ number_format($amenity->pivot->price, 2) }} PLN
                                                    @else
                                                        <span class="text-success">Gratis</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted small mb-0">Brak przypisanych udogodnień.</p>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm border-0 border-top border-primary border-4 mb-4">
                    <div class="card-header bg-white">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                            <h2 class="h5 fw-bold mb-0">
                                @if ($staySearch['active'])
                                    Wolne pokoje w wybranym terminie
                                @else
                                    Dostępne pokoje
                                @endif
                            </h2>
                            <form method="GET" action="{{ route('hotels.show', $hotel) }}" class="d-flex align-items-center gap-2">
                                @foreach (request()->except(['room_sort', 'page']) as $key => $value)
                                    @if (is_array($value))
                                        @continue
                                    @endif
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <label for="room_sort" class="small text-muted mb-0 text-nowrap">Sortuj:</label>
                                <select id="room_sort" name="room_sort" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="price_asc" @selected($roomSort === 'price_asc')>Cena rosnąco</option>
                                    <option value="price_desc" @selected($roomSort === 'price_desc')>Cena malejąco</option>
                                    <option value="name_asc" @selected($roomSort === 'name_asc')>Nazwa A–Z</option>
                                    <option value="name_desc" @selected($roomSort === 'name_desc')>Nazwa Z–A</option>
                                    <option value="capacity_asc" @selected($roomSort === 'capacity_asc')>Miejsca rosnąco</option>
                                    <option value="capacity_desc" @selected($roomSort === 'capacity_desc')>Miejsca malejąco</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    @auth
                        @if (auth()->user()->canAccessHotelPanel())
                            <div class="px-3 pt-3">
                                <a href="{{ route('manage.rooms.create', $hotel) }}" class="btn btn-success w-100 btn-sm">
                                    Dodaj pokój (panel)
                                </a>
                            </div>
                        @endif
                    @endauth

                    <ul class="list-group list-group-flush mt-2">
                        @forelse ($rooms as $room)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h3 class="h6 fw-bold mb-0">{{ $room->name }}</h3>
                                    <span class="badge bg-primary">{{ number_format($room->price_per_night, 2) }} PLN / noc</span>
                                </div>
                                <p class="small text-muted mb-2">{{ Str::limit($room->description, 120) }}</p>

                                @include('partials.room-public-photo-thumbs', ['room' => $room])

                                <p class="small mb-2">
                                    <span class="badge bg-light text-dark border">{{ $room->capacity }} os.</span>
                                    <span class="badge bg-light text-dark border">{{ $room->quantity }} szt.</span>
                                </p>

                                @if ($room->roomAmenities->isNotEmpty())
                                    <div class="small mb-2">
                                        <strong class="text-success">W standardzie:</strong>
                                        @forelse ($room->standardAmenities() as $roomAmenity)
                                            <span class="badge bg-light text-success border me-1 mb-1">
                                                {{ $roomAmenity->hotelAmenity->amenity->name }}
                                            </span>
                                        @empty
                                            <span class="text-muted">—</span>
                                        @endforelse
                                    </div>
                                    <div class="small mb-3">
                                        <strong>Opcjonalnie płatne (wybór przy rezerwacji):</strong>
                                        @if ($room->optionalPaidAmenities()->isNotEmpty())
                                            <ul class="mb-0 ps-3 mt-1">
                                                @foreach ($room->optionalPaidAmenities() as $roomAmenity)
                                                    <li>
                                                        {{ $roomAmenity->hotelAmenity->amenity->name }}
                                                        — {{ number_format($roomAmenity->price, 2) }} PLN
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted mb-0 mt-1">Brak płatnych dodatków.</p>
                                        @endif
                                    </div>
                                @endif

                                @auth
                                    <a href="{{ route('bookings.create', [$hotel, $room]).($stayQuery !== [] ? '?'.http_build_query($stayQuery) : '') }}"
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
                                @if ($staySearch['active'])
                                    Brak wolnych pokoi w podanym terminie.
                                @else
                                    Brak dostępnych pokoi.
                                @endif
                            </li>
                        @endforelse
                    </ul>

                    @if ($rooms->hasPages())
                        <div class="card-footer bg-white border-top-0 pt-0 pb-3 px-3">
                            {{ $rooms->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Prawa kolumna: mapa, opinie, galeria (partials Macieja) --}}
            <div class="col-lg-5">
                @if ($hotel->latitude && $hotel->longitude)
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body pb-3">
                            <h2 class="h6 fw-bold mb-2">Lokalizacja</h2>
                            <div id="hotel-map-show"
                                 class="hotel-map-show--sidebar rounded border overflow-hidden"
                                 data-lat="{{ $hotel->latitude }}"
                                 data-lng="{{ $hotel->longitude }}"
                                 data-address="{{ $hotel->address }}, {{ $hotel->city }}">
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning mb-4">Hotel nie ma jeszcze zapisanych współrzędnych na mapie.</div>
                @endif

                @include('partials.hotel-reviews-public')

                @include('partials.hotel-public-photo-gallery')
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
