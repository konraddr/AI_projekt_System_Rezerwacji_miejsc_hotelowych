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
            @include('partials.alerts')

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

                    @if ($booking->room->roomAmenities->isNotEmpty())
                        @include('partials.booking-room-amenities', ['room' => $booking->room, 'booking' => $booking])
                    @endif

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('manage.hotels.bookings.index', $hotel) }}" class="btn btn-outline-secondary">
                            Powrót do listy
                        </a>
                        @if ($booking->canCancel())
                            <form action="{{ route('manage.hotels.bookings.cancel', [$hotel, $booking]) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Czy na pewno chcesz anulować tę rezerwację? Klient otrzyma powiadomienie.');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">Anuluj rezerwację</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
