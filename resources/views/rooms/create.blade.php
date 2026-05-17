@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-outline-secondary mb-4"> Powrót do hotelu</a>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white fs-5">
                        Dodaj nowy pokój do hotelu: {{ $hotel->name }}
                    </div>
                    <div class="card-body bg-light">
                        <form action="{{ route('rooms.store', $hotel) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nazwa pokoju (np. Apartament Królewski)</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Opis pokoju</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Opisz pokój, widok z okna, metraż..." required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Ilość osób (Pojemność)</label>
                                    <input type="number" name="capacity" class="form-control" min="1" placeholder="np. 2" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Cena za noc (PLN)</label>
                                    <input type="number" name="price_per_night" class="form-control" min="0" step="0.01" placeholder="np. 250" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Ilość takich pokoi</label>
                                    <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Wybierz udogodnienia dla tego pokoju:</label>
                                <div class="text-muted small mb-3">Zaznacz opcję i podaj jej cenę (zostaw puste lub wpisz 0, jeśli jest w cenie pobytu). Widzisz tu tylko udogodnienia dodane wcześniej do tego hotelu!</div>

                                @forelse($amenities as $amenity)
                                    <div class="form-check d-flex align-items-center w-100 mb-2">
                                        <input class="form-check-input me-2" type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity_{{ $amenity->id }}">
                                        <label class="form-check-label flex-grow-1" for="amenity_{{ $amenity->id }}">
                                            {{ $amenity->name }}
                                        </label>
                                        <div class="input-group input-group-sm w-25">
                                            <input type="number" name="amenity_prices[{{ $amenity->id }}]" class="form-control text-end" placeholder="0.00" min="0" step="0.01">
                                            <span class="input-group-text">PLN</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-warning">
                                        Właściciel nie zaznaczył żadnych udogodnień dla całego hotelu.
                                    </div>
                                @endforelse
                            </div>

                            <button type="submit" class="btn btn-success w-100 fs-5">Zapisz i dodaj pokój</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
