@if ($links['bookings'] ?? false)
    <a href="{{ route('manage.hotels.bookings.index', $hotel) }}" class="btn btn-sm btn-outline-info">
        Rezerwacje
    </a>
@endif
@if ($links['workers'] ?? false)
    <a href="{{ route('manage.hotels.workers.index', $hotel) }}" class="btn btn-sm btn-outline-secondary">
        Pracownicy
    </a>
@endif
@if ($links['rooms'] ?? false)
    <a href="{{ route('manage.rooms.index', $hotel) }}" class="btn btn-sm btn-outline-primary">
        Pokoje
    </a>
@endif
@if ($links['photos'] ?? false)
    <a href="{{ route('manage.hotels.photos.index', $hotel) }}" class="btn btn-sm btn-outline-info">
        Zdjęcia
    </a>
@endif
@if ($links['chat'] ?? false)
    <a href="{{ route('manage.hotels.chat', $hotel) }}" class="btn btn-sm btn-outline-primary">
        Czat
    </a>
@endif
