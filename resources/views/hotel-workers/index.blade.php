@extends('layouts.manage')

@section('title', 'Pracownicy — '.$hotel->name)

@section('manage-content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manage.hotels.index') }}">Hotele</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }} — pracownicy</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Pracownicy: {{ $hotel->name }}</h1>
            <p class="text-muted mb-0">Zarządzaj dostępem do panelu hotelu.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @include('partials.hotel-owner-links', ['hotel' => $hotel])
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Obecni pracownicy</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Użytkownik</th>
                                <th>E-mail</th>
                                <th class="text-end">Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($workers as $worker)
                                <tr>
                                    <td class="fw-semibold">
                                        {{ $worker->name }}
                                        @if ($worker->last_name)
                                            {{ $worker->last_name }}
                                        @endif
                                        @if ($worker->id === auth()->id())
                                            <span class="badge bg-light text-dark border">Ty</span>
                                        @endif
                                    </td>
                                    <td>{{ $worker->email }}</td>
                                    <td class="text-end">
                                        @if ($workers->count() > 1)
                                            <form action="{{ route('manage.hotels.workers.destroy', [$hotel, $worker]) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Usunąć tego pracownika z hotelu?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Usuń</button>
                                            </form>
                                        @else
                                            <span class="text-muted small">Ostatni pracownik</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Brak pracowników.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Dodaj pracownika</div>
                <div class="card-body">
                    @if ($assignableUsers->isEmpty())
                        <p class="text-muted mb-0">Wszyscy aktywni użytkownicy mają już dostęp do tego hotelu.</p>
                    @else
                        <form action="{{ route('manage.hotels.workers.store', $hotel) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="user_id">Użytkownik</label>
                                <select name="user_id" id="user_id"
                                        class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Wybierz…</option>
                                    @foreach ($assignableUsers as $user)
                                        <option value="{{ $user->id }}" @selected((int) old('user_id') === $user->id)>
                                            {{ $user->name }}
                                            @if ($user->last_name)
                                                {{ $user->last_name }}
                                            @endif
                                            ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Dodaj pracownika</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
