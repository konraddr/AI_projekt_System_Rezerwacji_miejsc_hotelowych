@extends('layouts.manage')

@section('title', 'Dodaj hotel')

@section('manage-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.hotels.index') }}">Hotele</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nowy hotel</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">Dodaj nowy hotel</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('manage.hotels.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="name">Nazwa hotelu</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="np. Grand Hotel Krynica" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="city">Miasto</label>
                                <input type="text" name="city" id="city"
                                       class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city') }}" required>
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="address">Adres</label>
                                <input type="text" name="address" id="address"
                                       class="form-control @error('address') is-invalid @enderror"
                                       value="{{ old('address') }}" required>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label class="form-label fw-semibold" for="description">Opis obiektu</label>
                            <textarea name="description" id="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @include('partials.hotel-photos-link')

                        @include('partials.hotel-map-form', [
                            'latitude' => old('latitude', 52.069),
                            'longitude' => old('longitude', 19.480),
                        ])

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Udogodnienia hotelu</label>
                            <p class="text-muted small">Zaznacz dostępne udogodnienia i ustaw cenę bazową (0 = gratis).</p>
                            @include('partials.amenities-selector', [
                                'amenities' => $amenities,
                                'selectedAmenities' => old('amenities', []),
                                'amenityPrices' => old('amenity_prices', []),
                            ])
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success">Zapisz hotel</button>
                            <a href="{{ route('manage.hotels.index') }}" class="btn btn-outline-secondary">Anuluj</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @include('partials.leaflet-assets', ['includeJs' => false])
    <link rel="stylesheet" href="{{ asset('css/hotel-map.css') }}">
@endpush

@push('scripts')
    @include('partials.leaflet-assets', ['includeJs' => true])
    <script src="{{ asset('js/hotel-map-form.js') }}"></script>
@endpush
