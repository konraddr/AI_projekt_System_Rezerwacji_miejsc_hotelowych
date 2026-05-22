@extends('layouts.app')

@section('title', 'Katalog hoteli')

@section('content')
    <div class="container py-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h2 mb-1">Znajdź idealny hotel</h1>
                <p class="text-muted mb-0">Przeglądaj obiekty, pokoje i udogodnienia.</p>
            </div>
            @auth
                <a href="{{ route('manage.hotels.index') }}" class="btn btn-outline-primary">Panel właściciela</a>
            @endauth
        </div>

        <div class="row g-4">
            @forelse ($hotels as $hotel)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $hotel->name }}</h5>
                                <span class="badge bg-secondary">{{ $hotel->city }}</span>
                            </div>
                            <p class="text-muted small mb-2">{{ $hotel->address }}</p>
                            <p class="card-text flex-grow-1">{{ Str::limit($hotel->description, 120) }}</p>

                            @if ($hotel->amenities->isNotEmpty())
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    @foreach ($hotel->amenities->take(4) as $amenity)
                                        <span class="badge bg-light text-dark border">{{ $amenity->name }}</span>
                                    @endforeach
                                    @if ($hotel->amenities->count() > 4)
                                        <span class="badge bg-light text-dark border">+{{ $hotel->amenities->count() - 4 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white border-top-0 pt-0 pb-3 px-3">
                            <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-primary w-100">Zobacz szczegóły</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center py-5">
                        <h5 class="alert-heading">Brak hoteli w katalogu</h5>
                        <p class="mb-0">Wkrótce pojawią się pierwsze obiekty noclegowe.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($hotels->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $hotels->links() }}
            </div>
        @endif
    </div>
@endsection
