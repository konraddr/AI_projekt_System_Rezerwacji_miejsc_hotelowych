@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fs-5">
                        <h1>Dodaj nowy hotel do systemu</h1>
                    </div>
                    <div class="card-body bg-light">
                        <form action="/hotels" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nazwa hotelu</label>
                                <input type="text" name="name" class="form-control" placeholder="np. Grand Hotel Krynica" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Miasto</label>
                                    <input type="text" name="city" class="form-control" placeholder="np. Krynica-Zdrój" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Adres ulicy</label>
                                    <input type="text" name="address" class="form-control" placeholder="np. ul. Zdrojowa 15" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Opis obiektu</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Napisz kilka słów o hotelu..." required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Zaznacz udogodnienia dostępne na start:</label><br>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($amenities as $amenity)
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
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 fs-5">Zapisz i dodaj hotel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
