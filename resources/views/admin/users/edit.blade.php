@extends('layouts.admin')

@section('title', 'Edytuj użytkownika')

@section('admin-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.admin.users.index') }}">Użytkownicy</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $user->email }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning">
                    <h1 class="h4 mb-0">Edytuj użytkownika: {{ $user->email }}</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('manage.admin.users.update', $user) }}" method="POST">
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
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="permission">Uprawnienie</label>
                                <select name="permission" id="permission"
                                        class="form-select @error('permission') is-invalid @enderror" required>
                                    @foreach (\App\Enums\UserPermission::cases() as $permissionCase)
                                        <option value="{{ $permissionCase->value }}"
                                            @selected((int) old('permission', $user->permission->value) === $permissionCase->value)>
                                            {{ $permissionCase->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('permission')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="password">Nowe hasło</label>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       autocomplete="new-password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Pozostaw puste, aby nie zmieniać hasła.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="password_confirmation">Potwierdź hasło</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                            <a href="{{ route('manage.admin.users.index') }}" class="btn btn-outline-secondary">Anuluj</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
