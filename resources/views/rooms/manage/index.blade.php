@extends('layouts.manage')

@section('title', 'Pokoje — '.$hotel->name)

@section('manage-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.hotels.index') }}">Hotele</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manage.rooms.index', $hotel) }}">{{ $hotel->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pokoje</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Pokoje: {{ $hotel->name }}</h1>
            <p class="text-muted mb-0">{{ $hotel->city }} — zarządzaj ofertą pokoi.</p>
        </div>
        <a href="{{ route('manage.rooms.create', $hotel) }}" class="btn btn-success">Dodaj pokój</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nazwa</th>
                        <th class="text-center">Pojemność</th>
                        <th class="text-end">Cena / noc</th>
                        <th class="text-center">Ilość</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hotel->rooms as $room)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $room->name }}</div>
                                <div class="small text-muted">{{ Str::limit($room->description, 60) }}</div>
                            </td>
                            <td class="text-center">{{ $room->capacity }} os.</td>
                            <td class="text-end">{{ number_format($room->price_per_night, 2) }} PLN</td>
                            <td class="text-center">{{ $room->quantity }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                                    <a href="{{ route('manage.rooms.edit', [$hotel, $room]) }}" class="btn btn-sm btn-outline-warning">Edytuj</a>
                                    @include('partials.delete-modal', [
                                        'modalId' => 'deleteRoom'.$room->id,
                                        'title' => 'Usuń pokój',
                                        'message' => 'Czy na pewno chcesz usunąć pokój „'.$room->name.'”?',
                                        'action' => route('manage.rooms.destroy', [$hotel, $room]),
                                    ])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <p class="mb-3">Ten hotel nie ma jeszcze pokoi.</p>
                                <a href="{{ route('manage.rooms.create', $hotel) }}" class="btn btn-success btn-sm">Dodaj pierwszy pokój</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
