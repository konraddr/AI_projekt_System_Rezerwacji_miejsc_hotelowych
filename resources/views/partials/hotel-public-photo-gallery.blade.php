@php
    $hotel->loadMissing(['photos', 'rooms.photos']);
@endphp

@if ($hotel->photos->isNotEmpty())
    @pushOnce('styles')
        <link rel="stylesheet" href="{{ asset('css/hotel-photo-gallery.css') }}">
    @endPushOnce

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h2 class="h5 fw-bold border-bottom pb-2 mb-3">Galeria obiektu</h2>
            @include('partials.hotel-photo-preview-strip', [
                'photos' => $hotel->photos,
                'carouselId' => 'hotelPhotoCarousel',
                'altPrefix' => 'Zdjęcie '.$hotel->name,
                'enlargeOnClick' => true,
            ])
        </div>
    </div>

    @include('partials.photo-lightbox')
@endif
