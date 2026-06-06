@extends('layouts.app')

@section('title', 'Edytuj opinię — '.$hotel->name)

@section('content')
    <div class="container py-2">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hotels.show', $hotel) }}">{{ $hotel->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edycja opinii</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning">
                        <h1 class="h4 mb-0">Edytuj opinię</h1>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('manage.hotels.reviews.update', [$hotel, $review]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="rating">Ocena</label>
                                <select name="rating" id="rating" class="form-select @error('rating') is-invalid @enderror" required>
                                    @for ($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}" @selected((int) old('rating', $review->rating) === $i)>{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="comment">Komentarz</label>
                                <textarea name="comment" id="comment" rows="5"
                                          class="form-control @error('comment') is-invalid @enderror"
                                          required>{{ old('comment', $review->comment) }}</textarea>
                                @error('comment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-warning">Zapisz zmiany</button>
                                <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-outline-secondary">Anuluj</a>
                            </div>
                        </form>

                        <form action="{{ route('manage.hotels.reviews.destroy', [$hotel, $review]) }}" method="POST"
                              class="mt-3" onsubmit="return confirm('Czy na pewno usunąć opinię?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Usuń opinię</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
