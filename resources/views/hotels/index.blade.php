@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Nasze Hotele</h1>

        <div class="row">
            @foreach($hotels as $hotel)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $hotel->name }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                 {{ $hotel->city }}, {{ $hotel->address }}
                            </h6>
                            <p class="card-text mt-3">
                                {{ Str::limit($hotel->description, 120) }}
                            </p>

                            <strong>Udogodnienia:</strong>
                            <ul class="mt-2">
                                @foreach($hotel->amenities as $amenity)
                                    <li>{{ $amenity->name }} <span class="badge bg-success">{{ $amenity->pivot->price }} PLN</span></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-primary w-100">Zobacz szczegóły</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
