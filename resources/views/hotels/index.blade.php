@extends('layouts.app')

@section('title', 'Katalog hoteli')

@section('content')
    <div class="container py-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h2 mb-1">Znajdź idealny hotel</h1>
                <p class="text-muted mb-0">Przeglądaj obiekty, pokoje i udogodnienia.</p>
            </div>
            @auth
                @if (auth()->user()->canAccessHotelPanel())
                    <a href="{{ route('manage.hotels.index') }}" class="btn btn-outline-primary">Panel hoteli</a>
                @endif
            @endauth
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('hotels.index') }}" class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="check_in" class="form-label">Przyjazd</label>
                        <input type="date" id="check_in" name="check_in" class="form-control"
                               value="{{ request('check_in') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="check_out" class="form-label">Wyjazd</label>
                        <input type="date" id="check_out" name="check_out" class="form-control"
                               value="{{ request('check_out') }}">
                    </div>
                    <div class="col-12 col-md-2">
                        <label for="guests" class="form-label">Goście</label>
                        <input type="number" id="guests" name="guests" class="form-control" min="1"
                               value="{{ request('guests') }}" placeholder="np. 2">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="q" class="form-label">Szukaj</label>
                        <input type="search"
                               id="q"
                               name="q"
                               class="form-control"
                               value="{{ request('q') }}"
                               placeholder="Nazwa, miasto, adres...">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="city" class="form-label">Miasto</label>
                        <select id="city" name="city" class="form-select">
                            <option value="">Wszystkie miasta</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="sort" class="form-label">Sortowanie</label>
                        <select id="sort" name="sort" class="form-select">
                            <option value="">Najnowsze</option>
                            <option value="name_asc" @selected(request('sort') === 'name_asc')>Nazwa A–Z</option>
                            <option value="name_desc" @selected(request('sort') === 'name_desc')>Nazwa Z–A</option>
                            <option value="city_asc" @selected(request('sort') === 'city_asc')>Miasto A–Z</option>
                            <option value="city_desc" @selected(request('sort') === 'city_desc')>Miasto Z–A</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Filtruj</button>
                        @if (request()->hasAny(['q', 'city', 'sort', 'check_in', 'check_out', 'guests']))
                            <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary">Wyczyść</a>
                        @endif
                    </div>
                </form>
                @if ($staySearch['active'])
                    <p class="text-muted small mb-0 mt-3">
                        Pokazuję hotele z wolnymi pokojami w terminie
                        {{ $staySearch['check_in'] }} — {{ $staySearch['check_out'] }}
                        @if ($staySearch['guests'])
                            dla {{ $staySearch['guests'] }} {{ $staySearch['guests'] === 1 ? 'gościa' : 'gości' }}.
                        @endif
                    </p>
                @endif
            </div>
        </div>

        <div class="row g-4">
            @forelse ($hotels as $hotel)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 rounded-3">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $hotel->name }}</h5>
                                <span class="badge bg-secondary">{{ $hotel->city }}</span>
                            </div>
                            <p class="text-muted small mb-2">{{ $hotel->address }}</p>
                            <p class="card-text flex-grow-1">{{ Str::limit($hotel->description, 120) }}</p>

                            @if ($hotel->amenities->isNotEmpty())
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    @foreach ($hotel->amenities->take(4) as $amenity)
                                        <span class="badge bg-light text-dark border">{{ $amenity->name }}</span>
                                    @endforeach
                                    @if ($hotel->amenities->count() > 4)
                                        <span class="badge bg-light text-dark border">+{{ $hotel->amenities->count() - 4 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white border-top-0 pt-0 pb-3 px-3">
                            <a href="{{ route('hotels.show', $hotel) }}{{ $staySearch['active'] ? '?'.http_build_query(array_filter(['check_in' => $staySearch['check_in'], 'check_out' => $staySearch['check_out'], 'guests' => $staySearch['guests']])) : '' }}"
                               class="btn btn-primary w-100">Zobacz szczegóły</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center py-5">
                        <h5 class="alert-heading">Brak hoteli</h5>
                        <p class="mb-0">Nie znaleziono obiektów dla podanych kryteriów.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($hotels->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $hotels->links() }}
            </div>
        @endif
    </div>
@endsection
