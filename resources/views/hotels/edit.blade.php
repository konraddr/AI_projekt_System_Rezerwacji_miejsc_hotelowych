@extends('layouts.manage')

@section('title', 'Edytuj hotel')

@section('manage-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.hotels.index') }}">Hotele</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning">
                    <h1 class="h4 mb-0">Edytuj hotel: {{ $hotel->name }}</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('manage.hotels.update', $hotel) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="name">Nazwa hotelu</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $hotel->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="city">Miasto</label>
                                <input type="text" name="city" id="city"
                                       class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city', $hotel->city) }}" required>
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="address">Adres</label>
                                <input type="text" name="address" id="address"
                                       class="form-control @error('address') is-invalid @enderror"
                                       value="{{ old('address', $hotel->address) }}" required>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label class="form-label fw-semibold" for="description">Opis</label>
                            <textarea name="description" id="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $hotel->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @include('partials.hotel-map-form', [
                            'latitude' => old('latitude', $hotel->latitude ?? 52.069),
                            'longitude' => old('longitude', $hotel->longitude ?? 19.480),
                        ])

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Udogodnienia hotelu</label>
                            @php
                                $selectedAmenities = old('amenities', $hotel->amenities->pluck('id')->all());
                                $amenityPrices = old('amenity_prices', $hotel->amenities->mapWithKeys(
                                    fn ($amenity) => [$amenity->id => $amenity->pivot->price]
                                )->all());
                            @endphp
                            @include('partials.amenities-selector', [
                                'amenities' => $amenities,
                                'selectedAmenities' => $selectedAmenities,
                                'amenityPrices' => $amenityPrices,
                            ])
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-warning">Zapisz zmiany</button>
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
