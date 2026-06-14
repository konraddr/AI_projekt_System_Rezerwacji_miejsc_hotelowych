@extends('layouts.manage')

@section('title', 'Zdjęcia — '.$hotel->name)

@section('manage-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.hotels.index') }}">Hotele</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }} — zdjęcia</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Zdjęcia hotelu</h1>
            <p class="text-muted mb-0">{{ $hotel->name }}, {{ $hotel->city }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('manage.hotels.index') }}" class="btn btn-outline-secondary">Lista hoteli</a>
            <a href="{{ route('manage.rooms.index', $hotel) }}" class="btn btn-outline-primary">Pokoje</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h2 class="h5 mb-0">Dodaj zdjęcie</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('manage.hotels.photos.store', $hotel) }}" method="POST" enctype="multipart/form-data"
                  class="row g-3 align-items-end">
                @csrf
                <div class="col-12 col-md-6">
                    <label for="photo" class="form-label fw-semibold">Plik (JPG, PNG)</label>
                    <input type="file" name="photo" id="photo"
                           class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg"
                           required>
                    @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-3">
                    <label for="order" class="form-label fw-semibold">Kolejność</label>
                    <input type="number" name="order" id="order" min="1" max="{{ $photos->count() + 1 }}" step="1"
                           class="form-control @error('order') is-invalid @enderror"
                           value="{{ old('order', ($photos->max('order') ?? 0) + 1) }}"
                           placeholder="opcjonalnie">
                    @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Niższa liczba = wyżej na liście.</div>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Prześlij zdjęcie</button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/hotel-photo-gallery.css') }}">
    @endpush

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Galeria ({{ $photos->count() }})</h2>
        </div>
        <div class="card-body">
            @if ($photos->isNotEmpty())
                <div class="mb-4 pb-3 border-bottom">
                    @include('partials.hotel-photo-preview-strip', [
                        'photos' => $photos,
                        'carouselId' => 'manageHotelPhotoCarousel',
                        'altPrefix' => 'Zdjęcie hotelu '.$hotel->name,
                    ])
                </div>
            @endif

            @forelse ($photos as $photo)
                <div class="row g-3 align-items-center border-bottom py-3 {{ $loop->last ? 'border-0 pb-0' : '' }}">
                    <div class="col-12 col-md-3 col-lg-2">
                        <img src="{{ $photo->url() }}" alt="Zdjęcie hotelu {{ $hotel->name }}"
                             class="img-fluid rounded border shadow-sm"
                             style="max-height: 120px; object-fit: cover; width: 100%;">
                    </div>
                    <div class="col-12 col-md-4">
                        <p class="small text-muted mb-1">Plik: {{ $photo->filename }}.{{ $photo->file_type }}</p>
                        <p class="small mb-0">ID: <code>{{ $photo->id }}</code></p>
                    </div>
                    <div class="col-12 col-md-3">
                        <form action="{{ route('manage.hotels.photos.update', [$hotel, $photo]) }}" method="POST"
                              class="d-flex gap-2 align-items-end">
                            @csrf
                            @method('PATCH')
                            <div class="flex-grow-1">
                                <label class="form-label small fw-semibold mb-1" for="order_{{ $photo->id }}">Kolejność</label>
                                <input type="number" name="order" id="order_{{ $photo->id }}" min="1" max="{{ $photos->count() }}"
                                       class="form-control form-control-sm @error('order', 'update-photo-'.$photo->id) is-invalid @enderror"
                                       value="{{ $errors->getBag('update-photo-'.$photo->id)->has('order') ? old('order') : $photo->order }}" required>
                                @error('order', 'update-photo-'.$photo->id)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-primary">Zapisz</button>
                        </form>
                    </div>
                    <div class="col-12 col-md-2 text-md-end">
                        @include('partials.delete-modal', [
                            'modalId' => 'deletePhotoModal'.$loop->index,
                            'title' => 'Usuń zdjęcie',
                            'message' => 'Czy na pewno chcesz usunąć to zdjęcie?',
                            'action' => route('manage.hotels.photos.destroy', [$hotel, $photo]),
                        ])
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-4 mb-0">Brak zdjęć. Dodaj pierwsze zdjęcie powyżej.</p>
            @endforelse
        </div>
    </div>
@endsection
