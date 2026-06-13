@if ($room->photos->isNotEmpty())
    @pushOnce('styles')
        <link rel="stylesheet" href="{{ asset('css/hotel-photo-gallery.css') }}">
    @endPushOnce

    <div class="room-photo-gallery mb-2">
        <p class="small fw-semibold mb-2">Galeria pokoju</p>
        @include('partials.hotel-photo-preview-strip', [
            'photos' => $room->photos,
            'carouselId' => 'roomPhotoCarousel'.$room->id,
            'altPrefix' => 'Zdjęcie '.$room->name,
            'enlargeOnClick' => true,
        ])
    </div>

    @include('partials.photo-lightbox')
@endif
