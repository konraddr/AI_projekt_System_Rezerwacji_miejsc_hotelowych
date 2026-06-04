@php
    $hotelReviews = $hotel->reviews()->visible()->with('user')->latest()->get();
    $userReview = auth()->check()
        ? $hotel->reviews()->where('user_id', auth()->id())->first()
        : null;
@endphp

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3 border-bottom pb-2">
            <h2 class="h5 fw-bold mb-0">Opinie gości</h2>
            @auth
                @if ($userReview)
                    <a href="{{ route('manage.hotels.reviews.edit', [$hotel, $userReview]) }}"
                       class="btn btn-sm btn-outline-primary">Edytuj swoją opinię</a>
                @else
                    <a href="{{ route('manage.hotels.reviews.create', $hotel) }}"
                       class="btn btn-sm btn-primary">Dodaj opinię</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Zaloguj się, aby dodać opinię</a>
            @endauth
        </div>

        @forelse ($hotelReviews as $review)
            <div class="border-bottom py-3 {{ $loop->last ? 'border-0 pb-0' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <strong>{{ $review->user->name }}</strong>
                    <span class="badge bg-warning text-dark">{{ $review->rating }} / 5</span>
                </div>
                <p class="mb-1 small text-muted">{{ $review->created_at->format('d.m.Y') }}</p>
                <p class="mb-0">{{ $review->comment }}</p>
            </div>
        @empty
            <p class="text-muted mb-0">Brak opinii. Bądź pierwszą osobą, która oceni ten obiekt.</p>
        @endforelse
    </div>
</div>
