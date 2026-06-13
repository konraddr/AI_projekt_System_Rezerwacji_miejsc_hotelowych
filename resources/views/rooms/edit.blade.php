@extends('layouts.manage')

@section('title', 'Edytuj pokój')

@section('manage-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.hotels.index') }}">Hotele</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manage.rooms.index', $hotel) }}">{{ $hotel->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $room->name }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning">
                    <h1 class="h4 mb-0">Edytuj pokój: {{ $room->name }}</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('manage.rooms.update', [$hotel, $room]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="name">Nazwa pokoju</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $room->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="description">Opis pokoju</label>
                            <textarea name="description" id="description" rows="3"
                                      class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $room->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="capacity">Pojemność</label>
                                <input type="number" name="capacity" id="capacity" min="1"
                                       class="form-control @error('capacity') is-invalid @enderror"
                                       value="{{ old('capacity', $room->capacity) }}" required>
                                @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="price_per_night">Cena / noc (PLN)</label>
                                <input type="number" name="price_per_night" id="price_per_night" min="0" step="0.01"
                                       class="form-control @error('price_per_night') is-invalid @enderror"
                                       value="{{ old('price_per_night', $room->price_per_night) }}" required>
                                @error('price_per_night')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="quantity">Ilość pokoi</label>
                                <input type="number" name="quantity" id="quantity" min="1"
                                       class="form-control @error('quantity') is-invalid @enderror"
                                       value="{{ old('quantity', $room->quantity) }}" required>
                                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Udogodnienia pokoju</label>
                            @include('partials.amenities-selector', [
                                'amenities' => $amenities,
                                'selectedAmenities' => $selectedAmenities,
                                'amenityPrices' => $amenityPrices,
                            ])
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-warning">Zapisz zmiany</button>
                            <a href="{{ route('manage.rooms.index', $hotel) }}" class="btn btn-outline-secondary">Anuluj</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
