@extends('layouts.app')

@section('title', 'Nowe zgłoszenie')

@section('content')
    <div class="container py-2">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manage.reports.index') }}">Zgłoszenia</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nowe</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h1 class="h4 mb-0">Nowe zgłoszenie</h1>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('manage.reports.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="title">Typ zgłoszenia</label>
                                <select name="title" id="title" class="form-select @error('title') is-invalid @enderror" required>
                                    <option value="" disabled @selected(old('title') === null)>Wybierz…</option>
                                    <option value="hotel_nie_odpowiada" @selected(old('title') === 'hotel_nie_odpowiada')>Hotel nie odpowiada</option>
                                    <option value="toksyczny_komentarz" @selected(old('title') === 'toksyczny_komentarz')>Toksyczny komentarz</option>
                                    <option value="inne" @selected(old('title') === 'inne')">Inne</option>
                                </select>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="reason">Opis problemu</label>
                                <textarea name="reason" id="reason" rows="6"
                                          class="form-control @error('reason') is-invalid @enderror"
                                          required minlength="10"
                                          placeholder="Opisz sytuację — np. nazwa hotelu, data kontaktu…">{{ old('reason', request('prefill')) }}</textarea>
                                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Wyślij zgłoszenie</button>
                                <a href="{{ route('manage.reports.index') }}" class="btn btn-outline-secondary">Anuluj</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
