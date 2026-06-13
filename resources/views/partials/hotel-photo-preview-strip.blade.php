@php
    $photoCount = $photos->count();
    $colClass = match (true) {
        $photoCount === 1 => 'col-12',
        $photoCount === 2 => 'col-6',
        default => 'col-4',
    };
    $carouselId = $carouselId ?? 'photoPreviewCarousel'.uniqid();
    $altPrefix = $altPrefix ?? 'Zdjęcie';
@endphp

@if ($photos->isNotEmpty())
    <div class="hotel-photo-preview-strip">
        @if ($photoCount <= 3)
            <div class="row g-2">
                @foreach ($photos as $photo)
                    <div class="{{ $colClass }}">
                        <img src="{{ $photo->url() }}"
                             class="hotel-photo-preview-img border{{ ($enlargeOnClick ?? false) ? ' hotel-photo-enlarge' : '' }}"
                             @if ($enlargeOnClick ?? false)
                                 role="button"
                                 data-bs-toggle="modal"
                                 data-bs-target="#hotelPhotoLightbox"
                                 data-full-src="{{ $photo->url() }}"
                             @endif
                             alt="{{ $altPrefix }} {{ $loop->iteration }}">
                    </div>
                @endforeach
            </div>
        @else
            @php $previewIndex = 0; @endphp
            <div id="{{ $carouselId }}" class="carousel slide hotel-photo-preview-carousel" data-bs-ride="false">
                <div class="carousel-inner">
                    @foreach ($photos->chunk(3) as $chunk)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="row g-2">
                                @foreach ($chunk as $photo)
                                    @php $previewIndex++; @endphp
                                    <div class="col-4">
                                        <img src="{{ $photo->url() }}"
                                             class="hotel-photo-preview-img border{{ ($enlargeOnClick ?? false) ? ' hotel-photo-enlarge' : '' }}"
                                             @if ($enlargeOnClick ?? false)
                                                 role="button"
                                                 data-bs-toggle="modal"
                                                 data-bs-target="#hotelPhotoLightbox"
                                                 data-full-src="{{ $photo->url() }}"
                                             @endif
                                             alt="{{ $altPrefix }} {{ $previewIndex }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button"
                        data-bs-target="#{{ $carouselId }}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Poprzednie zdjęcia</span>
                </button>
                <button class="carousel-control-next" type="button"
                        data-bs-target="#{{ $carouselId }}" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Następne zdjęcia</span>
                </button>
            </div>
        @endif
    </div>
@endif
