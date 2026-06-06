@php
    $hotel->loadMissing(['photos', 'rooms.photos']);
@endphp

@if ($hotel->photos->isNotEmpty())
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h2 class="h5 fw-bold border-bottom pb-2 mb-3">Galeria obiektu</h2>
            <div id="hotelPhotoCarousel" class="carousel slide rounded overflow-hidden border" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($hotel->photos as $photo)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <img src="{{ $photo->url() }}" class="d-block w-100"
                                 alt="Zdjęcie {{ $hotel->name }}"
                                 style="max-height: 360px; object-fit: cover;">
                        </div>
                    @endforeach
                </div>
                @if ($hotel->photos->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#hotelPhotoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Poprzednie</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#hotelPhotoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Następne</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif
