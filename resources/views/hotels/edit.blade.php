@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <a href="{{ route('manage.hotels.index') }}" class="btn btn-outline-secondary mb-4">Powrót do listy hoteli</a>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-dark fs-5">
                        Edytuj hotel: {{ $hotel->name }}
                    </div>
                    <div class="card-body bg-light">
                        <form action="{{ route('manage.hotels.update', $hotel) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nazwa hotelu</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $hotel->name) }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Miasto</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $hotel->city) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Adres</label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address', $hotel->address) }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Opis</label>
                                <textarea name="description" class="form-control" rows="4" required>{{ old('description', $hotel->description) }}</textarea>
                            </div>

                            <input type="hidden" name="latitude" value="{{ old('latitude', $hotel->latitude) }}">
                            <input type="hidden" name="longitude" value="{{ old('longitude', $hotel->longitude) }}">

                            <div class="mb-4">
                                <label class="form-label fw-bold">Udogodnienia hotelu</label>
                                @php
                                    $selectedAmenities = old('amenities', $hotel->amenities->pluck('id')->all());
                                @endphp
                                @foreach ($amenities as $amenity)
                                    @php
                                        $pivot = $hotel->amenities->firstWhere('id', $amenity->id)?->pivot;
                                    @endphp
                                    <div class="form-check d-flex align-items-center w-100 mb-2">
                                        <input class="form-check-input me-2" type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity_{{ $amenity->id }}"
                                            @checked(in_array($amenity->id, $selectedAmenities))>
                                        <label class="form-check-label flex-grow-1" for="amenity_{{ $amenity->id }}">{{ $amenity->name }}</label>
                                        <div class="input-group input-group-sm w-25">
                                            <input type="number" name="amenity_prices[{{ $amenity->id }}]" class="form-control text-end"
                                                value="{{ old('amenity_prices.'.$amenity->id, $pivot?->price ?? 0) }}" min="0" step="0.01">
                                            <span class="input-group-text">PLN</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-warning w-100">Zapisz zmiany</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
