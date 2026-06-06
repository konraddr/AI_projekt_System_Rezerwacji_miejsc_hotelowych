@if ($room->photos->isNotEmpty())
    <div class="d-flex flex-wrap gap-1 mb-2">
        @foreach ($room->photos->take(4) as $photo)
            <img src="{{ $photo->url() }}" alt="Zdjęcie {{ $room->name }}"
                 class="rounded border"
                 style="width: 52px; height: 52px; object-fit: cover;">
        @endforeach
        @if ($room->photos->count() > 4)
            <span class="badge bg-light text-dark border align-self-center">+{{ $room->photos->count() - 4 }}</span>
        @endif
    </div>
@endif
