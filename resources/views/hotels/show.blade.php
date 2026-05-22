@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary mb-4">⬅ Powrót do listy</a>

        <div class="row">
            <div class="col-md-8">
                <h1 class="display-5">{{ $hotel->name }}</h1>
                <h5 class="text-muted mb-4"> {{ $hotel->city }}, {{ $hotel->address }}</h5>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h4 class="fw-bold border-bottom pb-2">O obiekcie</h4>
                        <p class="mt-3" style="line-height: 1.8;">{{ $hotel->description }}</p>

                        <h5 class="mt-4 fw-bold">Udogodnienia obiektu:</h5>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            @foreach($hotel->amenities as $amenity)
                                <span class="badge bg-success fs-6 px-3 py-2">{{ $amenity->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 border-top border-primary border-4">
                    <div class="card-header bg-white fw-bold fs-5 py-3">
                        Dostępne pokoje
                    </div>
                    @auth
                        <div class="p-3 border-bottom text-center">
                            <a href="{{ route('manage.rooms.create', $hotel) }}" class="btn btn-success w-100">Dodaj nowy pokój</a>
                        </div>
                    @endauth
                    <ul class="list-group list-group-flush">
                        @forelse($hotel->rooms as $room)
                            <li class="list-group-item py-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 fw-bold">{{ $room->name }}</h6>
                                    <span class="badge bg-primary rounded-pill fs-6">{{ $room->price_per_night }} PLN</span>
                                </div>
                                <small class="text-muted">Pojemność: {{ $room->capacity }} os. | Ilość: {{ $room->quantity }} szt.</small>
                                <button class="btn btn-sm btn-outline-primary w-100 mt-2">Rezerwuj</button>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center py-4">Właściciel nie dodał jeszcze żadnych pokoi.</li>
                            @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
