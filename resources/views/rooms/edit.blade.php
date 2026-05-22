@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <a href="{{ route('manage.rooms.index', $hotel) }}" class="btn btn-outline-secondary mb-4">Powrót do listy pokoi</a>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-dark fs-5">
                        Edytuj pokój: {{ $room->name }}
                    </div>
                    <div class="card-body bg-light">
                        <form action="{{ route('manage.rooms.update', [$hotel, $room]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nazwa pokoju</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $room->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Opis pokoju</label>
                                <textarea name="description" class="form-control" rows="3" required>{{ old('description', $room->description) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Pojemność</label>
                                    <input type="number" name="capacity" class="form-control" min="1" value="{{ old('capacity', $room->capacity) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Cena za noc (PLN)</label>
                                    <input type="number" name="price_per_night" class="form-control" min="0" step="0.01" value="{{ old('price_per_night', $room->price_per_night) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Ilość pokoi</label>
                                    <input type="number" name="quantity" class="form-control" min="1" value="{{ old('quantity', $room->quantity) }}" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Udogodnienia pokoju</label>
                                @php
                                    $selectedAmenities = old('amenities', $room->roomAmenities
                                        ->map(fn ($item) => $item->hotelAmenity?->amenity_id)
                                        ->filter()
                                        ->all());
                                @endphp
                                @foreach ($amenities as $amenity)
                                    @php
                                        $roomAmenity = $room->roomAmenities->first(fn ($item) => $item->hotelAmenity?->amenity_id === $amenity->id);
                                    @endphp
                                    <div class="form-check d-flex align-items-center w-100 mb-2">
                                        <input class="form-check-input me-2" type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity_{{ $amenity->id }}"
                                            @checked(in_array($amenity->id, $selectedAmenities))>
                                        <label class="form-check-label flex-grow-1" for="amenity_{{ $amenity->id }}">{{ $amenity->name }}</label>
                                        <div class="input-group input-group-sm w-25">
                                            <input type="number" name="amenity_prices[{{ $amenity->id }}]" class="form-control text-end"
                                                value="{{ old('amenity_prices.'.$amenity->id, $roomAmenity?->price ?? 0) }}" min="0" step="0.01">
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
