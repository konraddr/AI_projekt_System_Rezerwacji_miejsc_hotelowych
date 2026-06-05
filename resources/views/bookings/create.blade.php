@extends('layouts.app')

@section('title', 'Rezerwacja — ' . $room->name)

@section('content')
    <div class="container py-2">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hotels.index') }}">Katalog</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hotels.show', $hotel) }}">{{ $hotel->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Rezerwacja</li>
            </ol>
        </nav>

        @include('partials.alerts')

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 border-top border-primary border-4">
                    <div class="card-header bg-white">
                        <h1 class="h4 fw-bold mb-1">Rezerwacja pokoju</h1>
                        <p class="text-muted mb-0">{{ $room->name }} — {{ $hotel->name }}</p>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">Cena za noc</span>
                                <span>{{ number_format($room->price_per_night, 2) }} PLN</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Dostępnych jednostek tego pokoju</span>
                                <span class="badge bg-secondary">{{ $room->quantity }} szt.</span>
                            </div>
                        </div>

                        <form action="{{ route('bookings.store', [$hotel, $room]) }}" method="POST">
                            @csrf

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="check_in">Data przyjazdu</label>
                                    <input type="date" name="check_in" id="check_in"
                                           class="form-control @error('check_in') is-invalid @enderror"
                                           value="{{ old('check_in') }}" required>
                                    @error('check_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="check_out">Data wyjazdu</label>
                                    <input type="date" name="check_out" id="check_out"
                                           class="form-control @error('check_out') is-invalid @enderror"
                                           value="{{ old('check_out') }}" required>
                                    @error('check_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            @if ($paidAmenities->isNotEmpty())
                                <div class="mb-4">
                                    <h2 class="h6 fw-bold mb-3">Dodatkowe płatne udogodnienia</h2>
                                    <p class="small text-muted">Ceny zostaną zamrożone w momencie rezerwacji.</p>
                                    @foreach ($paidAmenities as $roomAmenity)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                   name="extra_amenities[]"
                                                   id="extra_{{ $roomAmenity->id }}"
                                                   value="{{ $roomAmenity->id }}"
                                                   @checked(in_array($roomAmenity->id, old('extra_amenities', [])))>
                                            <label class="form-check-label" for="extra_{{ $roomAmenity->id }}">
                                                {{ $roomAmenity->hotelAmenity->amenity->name }}
                                                — {{ number_format($roomAmenity->price, 2) }} PLN
                                            </label>
                                        </div>
                                    @endforeach
                                    @error('extra_amenities')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                            @endif

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Potwierdź rezerwację</button>
                                <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-outline-secondary">Anuluj</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
