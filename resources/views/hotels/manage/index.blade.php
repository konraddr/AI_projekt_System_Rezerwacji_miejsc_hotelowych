@extends('layouts.manage')

@section('title', 'Zarządzanie hotelami')

@section('manage-content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Moje hotele</h1>
            <p class="text-muted mb-0">Zarządzaj obiektami, pokojami i udogodnieniami.</p>
        </div>
        <a href="{{ route('manage.hotels.create') }}" class="btn btn-primary">Dodaj hotel</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nazwa</th>
                        <th>Miasto</th>
                        <th class="text-center">Pokoje</th>
                        <th class="text-center">Udogodnienia</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hotels as $hotel)
                        <tr>
                            <td class="fw-semibold">{{ $hotel->name }}</td>
                            <td>{{ $hotel->city }}</td>
                            <td class="text-center">{{ $hotel->rooms_count }}</td>
                            <td class="text-center">{{ $hotel->amenities_count }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                                    <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-sm btn-outline-secondary">Podgląd</a>
                                    @include('partials.hotel-owner-links', ['hotel' => $hotel])
                                    <a href="{{ route('manage.hotels.edit', $hotel) }}" class="btn btn-sm btn-outline-warning">Edytuj</a>
                                    @include('partials.delete-modal', [
                                        'modalId' => 'deleteHotel'.$hotel->id,
                                        'title' => 'Usuń hotel',
                                        'message' => 'Czy na pewno chcesz usunąć hotel „'.$hotel->name.'”? Tej operacji nie można cofnąć.',
                                        'action' => route('manage.hotels.destroy', $hotel),
                                    ])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <p class="mb-3">Nie masz jeszcze żadnych hoteli.</p>
                                <a href="{{ route('manage.hotels.create') }}" class="btn btn-primary btn-sm">Dodaj pierwszy hotel</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
