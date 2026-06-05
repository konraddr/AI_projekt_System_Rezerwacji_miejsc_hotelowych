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
                                <p class="fw-semibold mb-0 text-capitalize">{{ $booking->status->value }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small mb-1">Status płatności</p>
                                <p class="fw-semibold mb-0 text-capitalize">{{ $booking->payment_status->value }}</p>
                            </div>
                        </div>

                        @if ($booking->extraAmenities->isNotEmpty())
                            <div class="mb-4">
                                <h2 class="h6 fw-bold border-bottom pb-2 mb-3">Zamrożone udogodnienia dodatkowe</h2>
                                <ul class="list-group list-group-flush">
                                    @foreach ($booking->extraAmenities as $extra)
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span>{{ $extra->hotelAmenity->amenity->name }}</span>
                                            <span>{{ number_format($extra->price, 2) }} PLN</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <span class="fw-bold">Łączna kwota</span>
                            <span class="fs-5 fw-bold text-primary">{{ number_format($booking->total_price, 2) }} PLN</span>
                        </div>

                        <p class="text-muted small mt-3 mb-0">
                            Płatność zostanie obsłużona w kolejnym etapie modułu rezerwacji.
                        </p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('bookings.index') }}" class="btn btn-outline-primary">Moje rezerwacje</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
