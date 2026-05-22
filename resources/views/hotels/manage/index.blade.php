@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Zarządzanie hotelami</h1>
            <a href="{{ route('manage.hotels.create') }}" class="btn btn-primary">Dodaj hotel</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>Miasto</th>
                        <th>Pokoje</th>
                        <th>Udogodnienia</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hotels as $hotel)
                        <tr>
                            <td>{{ $hotel->name }}</td>
                            <td>{{ $hotel->city }}</td>
                            <td>{{ $hotel->rooms_count }}</td>
                            <td>{{ $hotel->amenities_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-sm btn-outline-secondary">Podgląd</a>
                                <a href="{{ route('manage.rooms.index', $hotel) }}" class="btn btn-sm btn-outline-primary">Pokoje</a>
                                <a href="{{ route('manage.hotels.edit', $hotel) }}" class="btn btn-sm btn-outline-warning">Edytuj</a>
                                <form action="{{ route('manage.hotels.destroy', $hotel) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Usunąć hotel?')">Usuń</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Brak hoteli. Dodaj pierwszy obiekt.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
