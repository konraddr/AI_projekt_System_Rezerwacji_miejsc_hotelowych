@extends('layouts.app')

@section('title', 'Mój profil')

@section('content')
    <div class="container py-2">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h1 class="h4 mb-0">Mój profil</h1>
                    </div>
                    <div class="card-body">
                        @include('partials.alerts')

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="name">Imię</label>
                                    <input type="text" name="name" id="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="last_name">Nazwisko</label>
                                    <input type="text" name="last_name" id="last_name"
                                           class="form-control @error('last_name') is-invalid @enderror"
                                           value="{{ old('last_name', $user->last_name) }}">
                                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="email">E-mail</label>
                                    <input type="email" name="email" id="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="phone">Telefon</label>
                                    <input type="text" name="phone" id="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <h2 class="h6 fw-bold mb-3">Zmiana hasła</h2>
                            <p class="text-muted small">Pozostaw puste, jeśli nie chcesz zmieniać hasła.</p>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="current_password">Aktualne hasło</label>
                                    <input type="password" name="current_password" id="current_password"
                                           class="form-control @error('current_password') is-invalid @enderror"
                                           autocomplete="current-password">
                                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="password">Nowe hasło</label>
                                    <input type="password" name="password" id="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           autocomplete="new-password">
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="password_confirmation">Powtórz nowe hasło</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                           class="form-control"
                                           autocomplete="new-password">
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">Anuluj</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm border-danger">
                    <div class="card-body">
                        <h2 class="h6 fw-bold text-danger mb-2">Usuń konto</h2>
                        <p class="text-muted small mb-3">
                            Trwale usuniesz konto wraz z rezerwacjami i opiniami. Tej operacji nie można cofnąć.
                        </p>
                        <form action="{{ route('profile.destroy') }}" method="POST"
                              onsubmit="return confirm('Czy na pewno chcesz trwale usunąć konto?');">
                            @csrf
                            @method('DELETE')

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="delete_password">Potwierdź hasłem</label>
                                <input type="password" name="password" id="delete_password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       autocomplete="current-password" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <button type="submit" class="btn btn-outline-danger">Usuń konto</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
