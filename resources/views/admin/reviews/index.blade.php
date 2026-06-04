@extends('layouts.manage')

@section('title', 'Moderacja opinii')

@section('manage-content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Moderacja opinii</h1>
            <p class="text-muted mb-0">Ukrywaj lub przywracaj komentarze (flaga is_banned).</p>
        </div>
    </div>

    <div class="btn-group mb-4" role="group">
        <a href="{{ route('manage.admin.reviews.index') }}"
           class="btn btn-sm {{ ! request('filter') ? 'btn-primary' : 'btn-outline-primary' }}">Wszystkie</a>
        <a href="{{ route('manage.admin.reviews.index', ['filter' => 'visible']) }}"
           class="btn btn-sm {{ request('filter') === 'visible' ? 'btn-primary' : 'btn-outline-primary' }}">Widoczne</a>
        <a href="{{ route('manage.admin.reviews.index', ['filter' => 'banned']) }}"
           class="btn btn-sm {{ request('filter') === 'banned' ? 'btn-primary' : 'btn-outline-primary' }}">Ukryte</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Hotel</th>
                        <th>Autor</th>
                        <th class="text-center">Ocena</th>
                        <th>Komentarz</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr class="{{ $review->is_banned ? 'table-warning' : '' }}">
                            <td>
                                <a href="{{ route('hotels.show', $review->hotel) }}">{{ $review->hotel->name }}</a>
                            </td>
                            <td>{{ $review->user->name }}</td>
                            <td class="text-center">{{ $review->rating }}/5</td>
                            <td>{{ Str::limit($review->comment, 80) }}</td>
                            <td class="text-center">
                                @if ($review->is_banned)
                                    <span class="badge bg-danger">Ukryta</span>
                                @else
                                    <span class="badge bg-success">Widoczna</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if ($review->is_banned)
                                    <form action="{{ route('manage.admin.reviews.unban', $review) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-success">Przywróć</button>
                                    </form>
                                @else
                                    <form action="{{ route('manage.admin.reviews.ban', $review) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Ukryć tę opinię na profilu publicznym?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Ukryj</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">Brak opinii do moderacji.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($reviews->hasPages())
            <div class="card-footer bg-white">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
@endsection
