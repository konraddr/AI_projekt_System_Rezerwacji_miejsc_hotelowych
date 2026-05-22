@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('manage.hotels.index') }}" class="btn btn-outline-secondary mb-4">Powrót do hoteli</a>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Pokoje: {{ $hotel->name }}</h1>
            <a href="{{ route('manage.rooms.create', $hotel) }}" class="btn btn-success">Dodaj pokój</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>Pojemność</th>
                        <th>Cena / noc</th>
                        <th>Ilość</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hotel->rooms as $room)
                        <tr>
                            <td>{{ $room->name }}</td>
                            <td>{{ $room->capacity }} os.</td>
                            <td>{{ $room->price_per_night }} PLN</td>
                            <td>{{ $room->quantity }}</td>
                            <td class="text-end">
                                <a href="{{ route('manage.rooms.edit', [$hotel, $room]) }}" class="btn btn-sm btn-outline-warning">Edytuj</a>
                                <form action="{{ route('manage.rooms.destroy', [$hotel, $room]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Usunąć pokój?')">Usuń</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Brak pokoi w tym hotelu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
