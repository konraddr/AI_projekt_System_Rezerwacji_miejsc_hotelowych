@extends('layouts.manage')

@section('title', 'Edytuj udogodnienie')

@section('manage-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.amenities.index') }}">Udogodnienia</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $amenity->name }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning">
                    <h1 class="h4 mb-0">Edytuj: {{ $amenity->name }}</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('manage.amenities.update', $amenity) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="name">Nazwa</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $amenity->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">Zapisz zmiany</button>
                            <a href="{{ route('manage.amenities.index') }}" class="btn btn-outline-secondary">Anuluj</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
