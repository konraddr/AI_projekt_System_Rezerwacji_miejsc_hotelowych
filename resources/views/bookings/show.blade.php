@extends('layouts.app')

@section('title', 'Rezerwacja #' . $booking->id)

@section('content')
    <div class="container py-2">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('bookings.index') }}">Moje rezerwacje</a></li>
                <li class="breadcrumb-item active" aria-current="page">#{{ $booking->id }}</li>
            </ol>
        </nav>

        @include('partials.alerts')

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h1 class="h4 fw-bold mb-0">Potwierdzenie rezerwacji</h1>
                        <span class="badge bg-primary">#{{ $booking->id }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <p class="text-muted small mb-1">Hotel</p>
                                <p class="fw-semibold mb-0">
                                    <a href="{{ route('hotels.show', $booking->room->hotel) }}">
                                        {{ $booking->room->hotel->name }}
                                    </a>
                                </p>
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
                                <p class="text-muted small mb-1">Status rezerwacji</p>
                                <p class="fw-semibold mb-0">{{ $booking->status->label() }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small mb-1">Status płatności</p>
                                <p class="fw-semibold mb-0">{{ $booking->payment_status->label() }}</p>
                            </div>
                        </div>

                        @if ($booking->room->roomAmenities->isNotEmpty())
                            @include('partials.booking-room-amenities', ['room' => $booking->room, 'booking' => $booking])
                        @endif

                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <span class="fw-bold">Łączna kwota</span>
                            <span class="fs-5 fw-bold text-primary">{{ number_format($booking->total_price, 2) }} PLN</span>
                        </div>

                        @if ($booking->canPay())
                            <div class="mt-4 p-3 border rounded">
                                <h2 class="h6 fw-bold mb-2">Symulacja płatności</h2>
                                <p class="small text-muted mb-3">
                                    Brak prawdziwej bramki płatniczej — wybierz wynik transakcji testowej.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <form action="{{ route('bookings.pay', $booking) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            Opłać {{ number_format($booking->total_price, 2) }} PLN
                                        </button>
                                    </form>
                                    <form action="{{ route('bookings.fail-payment', $booking) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger">
                                            Symuluj nieudaną płatność
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-white d-flex flex-wrap gap-2">
                        <a href="{{ route('bookings.index') }}" class="btn btn-outline-primary">Moje rezerwacje</a>
                        @if ($booking->canCancel())
                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST"
                                  onsubmit="return confirm('Czy na pewno chcesz anulować tę rezerwację?');">
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
