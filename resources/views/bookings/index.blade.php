@extends('layouts.app')

@section('title', 'Moje rezerwacje')

@section('content')
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0">Moje rezerwacje</h1>
            <a href="{{ route('hotels.index') }}" class="btn btn-outline-primary btn-sm">Przeglądaj hotele</a>
        </div>

        @include('partials.alerts')

        @forelse ($bookings as $booking)
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <h2 class="h5 fw-bold mb-1">{{ $booking->room->hotel->name }}</h2>
                            <p class="text-muted mb-0">{{ $booking->room->name }}</p>
                        </div>
                        <span class="badge bg-primary fs-6">{{ number_format($booking->total_price, 2) }} PLN</span>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-dark border">
                            {{ $booking->check_in->format('d.m.Y') }} — {{ $booking->check_out->format('d.m.Y') }}
                        </span>
                        <span class="badge bg-secondary">{{ $booking->status->label() }}</span>
                        <span class="badge @if ($booking->payment_status->value === 'paid') bg-success @elseif ($booking->payment_status->value === 'failed') bg-danger @else bg-warning text-dark @endif">
                            {{ $booking->payment_status->label() }}
                        </span>
                    </div>

                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                        Szczegóły
                    </a>
                </div>
            </div>
        @empty
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5 text-muted">
                    <p class="mb-3">Nie masz jeszcze żadnych rezerwacji.</p>
                    <a href="{{ route('hotels.index') }}" class="btn btn-primary">Znajdź hotel</a>
                </div>
            </div>
        @endforelse
    </div>
@endsection
