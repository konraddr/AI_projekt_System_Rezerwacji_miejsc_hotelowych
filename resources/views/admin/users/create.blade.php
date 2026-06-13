@extends('layouts.admin')

@section('title', 'Dodaj użytkownika')

@section('admin-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.admin.users.index') }}">Użytkownicy</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nowy użytkownik</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">Dodaj użytkownika</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('manage.admin.users.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="name">Imię</label>
                                <input type="text" name="name" id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="last_name">Nazwisko</label>
                                <input type="text" name="last_name" id="last_name"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       value="{{ old('last_name') }}">
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="email">E-mail</label>
                                <input type="email" name="email" id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="phone">Telefon</label>
                                <input type="text" name="phone" id="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="permission">Uprawnienie</label>
                                <select name="permission" id="permission"
                                        class="form-select @error('permission') is-invalid @enderror" required>
                                    @foreach (\App\Enums\UserPermission::cases() as $permissionCase)
                                        <option value="{{ $permissionCase->value }}"
                                            @selected((int) old('permission', 5) === $permissionCase->value)>
                                            {{ $permissionCase->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('permission')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="password">Hasło</label>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       autocomplete="new-password" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="password_confirmation">Potwierdź hasło</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control" autocomplete="new-password" required>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Utwórz konto</button>
                            <a href="{{ route('manage.admin.users.index') }}" class="btn btn-outline-secondary">Anuluj</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
