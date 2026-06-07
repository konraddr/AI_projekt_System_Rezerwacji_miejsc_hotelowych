@extends('layouts.manage')

@section('title', 'Rezerwacja #'.$booking->id)

@section('manage-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.hotels.index') }}">Hotele</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('manage.hotels.bookings.index', $hotel) }}">{{ $hotel->name }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">#{{ $booking->id }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h1 class="h4 fw-bold mb-0">Rezerwacja #{{ $booking->id }}</h1>
                    <span class="badge bg-secondary">{{ $booking->status->label() }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Klient</p>
                            <p class="fw-semibold mb-0">
                                {{ $booking->user->name }}
                                @if ($booking->user->last_name)
                                    {{ $booking->user->last_name }}
                                @endif
                            </p>
                            <p class="text-muted small mb-0">{{ $booking->user->email }}</p>
                            @if ($booking->user->phone)
                                <p class="text-muted small mb-0">{{ $booking->user->phone }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Pokój</p>
                            <p class="fw-semibold mb-0">{{ $booking->room->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Przyjazd</p>
                            <p class="fw-semibold mb-0">{{ $booking->check_in->format('d.m.Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Wyjazd</p>
                            <p class="fw-semibold mb-0">{{ $booking->check_out->format('d.m.Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Status płatności</p>
                            <p class="fw-semibold mb-0">{{ $booking->payment_status->label() }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Kwota</p>
                            <p class="fw-semibold mb-0">{{ number_format($booking->total_price, 2) }} PLN</p>
                        </div>
                    </div>

                    @if ($booking->extraAmenities->isNotEmpty())
                        <h2 class="h6 fw-bold">Dodatkowe udogodnienia</h2>
                        <ul class="list-group list-group-flush mb-4">
                            @foreach ($booking->extraAmenities as $extra)
                                <li class="list-group-item px-0 d-flex justify-content-between">
                                    <span>{{ $extra->hotelAmenity?->amenity?->name ?? 'Udogodnienie' }}</span>
                                    <span>{{ number_format($extra->price, 2) }} PLN</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <a href="{{ route('manage.hotels.bookings.index', $hotel) }}" class="btn btn-outline-secondary">
                        Powrót do listy
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
