@extends('layouts.admin')

@section('title', $hotel->name)

@section('admin-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.admin.hotels.index') }}">Hotele</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }}</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">{{ $hotel->name }}</h1>
            <p class="text-muted mb-0">{{ $hotel->city }}, {{ $hotel->address }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-outline-secondary btn-sm">Widok publiczny</a>
            <a href="{{ route('manage.admin.hotels.edit', $hotel) }}" class="btn btn-warning btn-sm">Edytuj</a>
            @include('partials.delete-modal', [
                'modalId' => 'deleteAdminHotelShow'.$hotel->id,
                'title' => 'Usuń hotel',
                'message' => 'Czy na pewno chcesz usunąć hotel „'.$hotel->name.'”?',
                'action' => route('manage.admin.hotels.destroy', $hotel),
            ])
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">Dane obiektu</h2>
                    <p class="mb-2"><strong>Właściciel:</strong>
                        @if ($hotel->owner)
                            {{ $hotel->owner->name }} ({{ $hotel->owner->email }})
                        @else
                            —
                        @endif
                    </p>
                    <p class="mb-2"><strong>Pokoje:</strong> {{ $hotel->rooms_count }}</p>
                    <p class="mb-2"><strong>Opinie:</strong> {{ $hotel->reviews_count }}</p>
                    <p class="mb-2"><strong>Zdjęcia:</strong> {{ $hotel->photos_count }}</p>
                    <p class="mb-0"><strong>Współrzędne:</strong> {{ $hotel->latitude }}, {{ $hotel->longitude }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">Opis</h2>
                    <p class="mb-0" style="line-height: 1.7;">{{ $hotel->description }}</p>
                </div>
            </div>
        </div>
        @if ($hotel->amenities->isNotEmpty())
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">Udogodnienia</h2>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($hotel->amenities as $amenity)
                                <span class="badge bg-light text-dark border">
                                    {{ $amenity->name }}
                                    @if ((float) $amenity->pivot->price > 0)
                                        — {{ number_format($amenity->pivot->price, 2) }} PLN
                                    @else
                                        (gratis)
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
