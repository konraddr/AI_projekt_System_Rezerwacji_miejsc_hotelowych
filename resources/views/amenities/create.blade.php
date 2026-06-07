@extends('layouts.manage')

@section('title', 'Dodaj udogodnienie')

@section('manage-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.amenities.index') }}">Udogodnienia</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nowe</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">Dodaj udogodnienie</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('manage.amenities.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="name">Nazwa</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="np. Basen" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="icon">Ikona (opcjonalnie)</label>
                            <input type="text" name="icon" id="icon"
                                   class="form-control @error('icon') is-invalid @enderror"
                                   value="{{ old('icon') }}" placeholder="np. bi-water">
                            <div class="form-text">Nazwa klasy ikony Bootstrap Icons.</div>
                            @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Zapisz</button>
                            <a href="{{ route('manage.amenities.index') }}" class="btn btn-outline-secondary">Anuluj</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
