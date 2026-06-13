@extends('layouts.manage')

@section('title', 'Udogodnienia')

@section('manage-content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Katalog udogodnień</h1>
            <p class="text-muted mb-0">Lista udogodnień dostępnych przy dodawaniu hoteli i pokoi.</p>
        </div>
        <a href="{{ route('manage.amenities.create') }}" class="btn btn-primary">Dodaj udogodnienie</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nazwa</th>
                        <th>Ikona</th>
                        <th class="text-center">Hotele</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($amenities as $amenity)
                        <tr>
                            <td class="fw-semibold">{{ $amenity->name }}</td>
                            <td>{{ $amenity->icon ?? '—' }}</td>
                            <td class="text-center">{{ $amenity->hotels_count }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                                    <a href="{{ route('manage.amenities.edit', $amenity) }}"
                                       class="btn btn-sm btn-outline-warning">Edytuj</a>
                                    @if ($amenity->hotels_count === 0)
                                        @include('partials.delete-modal', [
                                            'modalId' => 'deleteAmenity'.$amenity->id,
                                            'title' => 'Usuń udogodnienie',
                                            'message' => 'Czy na pewno chcesz usunąć udogodnienie „'.$amenity->name.'”?',
                                            'action' => route('manage.amenities.destroy', $amenity),
                                        ])
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <p class="mb-3">Brak udogodnień w katalogu.</p>
                                <a href="{{ route('manage.amenities.create') }}" class="btn btn-primary btn-sm">Dodaj pierwsze</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
