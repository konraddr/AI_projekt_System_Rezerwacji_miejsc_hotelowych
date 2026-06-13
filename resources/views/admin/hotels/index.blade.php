@extends('layouts.admin')

@section('title', 'Zarządzanie hotelami')

@section('admin-content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Wszystkie hotele</h1>
            <p class="text-muted mb-0">Pełne zarządzanie obiektami w systemie.</p>
        </div>
        <a href="{{ route('manage.admin.hotels.create') }}" class="btn btn-primary">Dodaj hotel</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nazwa</th>
                        <th>Miasto</th>
                        <th>Właściciel</th>
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
                            <td class="small">
                                @if ($hotel->owner)
                                    {{ $hotel->owner->name }}<br>
                                    <span class="text-muted">{{ $hotel->owner->email }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $hotel->rooms_count }}</td>
                            <td class="text-center">{{ $hotel->amenities_count }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                                    <a href="{{ route('manage.admin.hotels.show', $hotel) }}" class="btn btn-sm btn-outline-primary">Szczegóły</a>
                                    <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-sm btn-outline-secondary">Publiczny</a>
                                    <a href="{{ route('manage.admin.hotels.edit', $hotel) }}" class="btn btn-sm btn-outline-warning">Edytuj</a>
                                    @include('partials.delete-modal', [
                                        'modalId' => 'deleteAdminHotel'.$hotel->id,
                                        'title' => 'Usuń hotel',
                                        'message' => 'Czy na pewno chcesz usunąć hotel „'.$hotel->name.'”? Tej operacji nie można cofnąć.',
                                        'action' => route('manage.admin.hotels.destroy', $hotel),
                                    ])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">Brak hoteli w systemie.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($hotels->hasPages())
            <div class="card-footer bg-white">
                {{ $hotels->links() }}
            </div>
        @endif
    </div>
@endsection
