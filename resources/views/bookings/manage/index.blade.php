@extends('layouts.manage')

@section('title', 'Rezerwacje — '.$hotel->name)

@section('manage-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.hotels.index') }}">Hotele</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }} — rezerwacje</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Rezerwacje: {{ $hotel->name }}</h1>
            <p class="text-muted mb-0">Przegląd rezerwacji w Twoim obiekcie.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @include('partials.hotel-owner-links', ['hotel' => $hotel])
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Klient</th>
                        <th>Pokój</th>
                        <th>Termin</th>
                        <th class="text-end">Kwota</th>
                        <th>Status</th>
                        <th>Płatność</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td class="fw-semibold">{{ $booking->id }}</td>
                            <td>
                                {{ $booking->user->name }}
                                @if ($booking->user->last_name)
                                    {{ $booking->user->last_name }}
                                @endif
                                <div class="small text-muted">{{ $booking->user->email }}</div>
                            </td>
                            <td>{{ $booking->room->name }}</td>
                            <td class="text-nowrap">
                                {{ $booking->check_in->format('d.m.Y') }} — {{ $booking->check_out->format('d.m.Y') }}
                            </td>
                            <td class="text-end">{{ number_format($booking->total_price, 2) }} PLN</td>
                            <td><span class="badge bg-secondary">{{ $booking->status->label() }}</span></td>
                            <td>
                                <span class="badge @if ($booking->payment_status->value === 'paid') bg-success @elseif ($booking->payment_status->value === 'failed') bg-danger @else bg-warning text-dark @endif">
                                    {{ $booking->payment_status->label() }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('manage.hotels.bookings.show', [$hotel, $booking]) }}"
                                   class="btn btn-sm btn-outline-primary">Szczegóły</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                Brak rezerwacji w tym hotelu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
