@extends('layouts.manage')

@section('title', 'Zdjęcia pokoju — '.$room->name)

@section('manage-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.hotels.index') }}">Hotele</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manage.rooms.index', $hotel) }}">{{ $hotel->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $room->name }} — zdjęcia</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Zdjęcia pokoju</h1>
            <p class="text-muted mb-0">{{ $room->name }} · {{ $hotel->name }}, {{ $hotel->city }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('manage.rooms.index', $hotel) }}" class="btn btn-outline-secondary">Lista pokoi</a>
            <a href="{{ route('manage.hotels.photos.index', $hotel) }}" class="btn btn-outline-info">Zdjęcia hotelu</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h2 class="h5 mb-0">Dodaj zdjęcie</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('manage.rooms.photos.store', [$hotel, $room]) }}" method="POST" enctype="multipart/form-data"
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
                    <input type="number" name="order" id="order" min="0" step="1"
                           class="form-control @error('order') is-invalid @enderror"
                           value="{{ old('order', ($photos->max('order') ?? -1) + 1) }}"
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

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Galeria ({{ $photos->count() }})</h2>
        </div>
        <div class="card-body">
            @forelse ($photos as $photo)
                <div class="row g-3 align-items-center border-bottom py-3 {{ $loop->last ? 'border-0 pb-0' : '' }}">
                    <div class="col-12 col-md-3 col-lg-2">
                        <img src="{{ $photo->url() }}" alt="Zdjęcie pokoju {{ $room->name }}"
                             class="img-fluid rounded border shadow-sm"
                             style="max-height: 120px; object-fit: cover; width: 100%;">
                    </div>
                    <div class="col-12 col-md-4">
                        <p class="small text-muted mb-1">Plik: {{ $photo->filename }}.{{ $photo->file_type }}</p>
                        <p class="small mb-0">ID: <code>{{ $photo->id }}</code></p>
                    </div>
                    <div class="col-12 col-md-3">
                        <form action="{{ route('manage.rooms.photos.update', [$hotel, $room, $photo]) }}" method="POST"
                              class="d-flex gap-2 align-items-end">
                            @csrf
                            @method('PATCH')
                            <div class="flex-grow-1">
                                <label class="form-label small fw-semibold mb-1" for="order_{{ $loop->index }}">Kolejność</label>
                                <input type="number" name="order" id="order_{{ $loop->index }}" min="0"
                                       class="form-control form-control-sm" value="{{ $photo->order }}" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-primary">Zapisz</button>
                        </form>
                    </div>
                    <div class="col-12 col-md-2 text-md-end">
                        @include('partials.delete-modal', [
                            'modalId' => 'deleteRoomPhotoModal'.$loop->index,
                            'title' => 'Usuń zdjęcie',
                            'message' => 'Czy na pewno chcesz usunąć to zdjęcie?',
                            'action' => route('manage.rooms.photos.destroy', [$hotel, $room, $photo]),
                        ])
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-4 mb-0">Brak zdjęć. Dodaj pierwsze zdjęcie powyżej.</p>
            @endforelse
        </div>
    </div>
@endsection
