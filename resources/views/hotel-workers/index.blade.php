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
            <p class="text-muted mb-0">Zarządzaj pracownikami hotelu. Dostęp mają właściciel, administrator oraz pracownicy z uprawnieniem „Pracownicy”.</p>
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
                                <th>Uprawnienia</th>
                                <th class="text-end">Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($workers as $worker)
                                @php
                                    $isPrivilegedManager = auth()->user()->hasPermission(\App\Enums\UserPermission::Administrator)
                                        || auth()->id() === $hotel->owner_id;
                                    $canManageWorker = $worker->id !== $hotel->owner_id
                                        && ($worker->id !== auth()->id() || $isPrivilegedManager);
                                    $workerPermissions = $worker->id === $hotel->owner_id
                                        ? array_map(fn ($access) => $access->value, \App\Enums\HotelWorkerAccess::cases())
                                        : (is_array($worker->pivot->permissions) ? $worker->pivot->permissions : []);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $worker->name }}
                                            @if ($worker->last_name)
                                                {{ $worker->last_name }}
                                            @endif
                                            @if ($worker->id === auth()->id())
                                                <span class="badge bg-light text-dark border">Ty</span>
                                            @endif
                                            @if ($worker->id === $hotel->owner_id)
                                                <span class="badge bg-primary">Właściciel</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted">{{ $worker->email }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach (\App\Enums\HotelWorkerAccess::cases() as $access)
                                                @if (in_array($access->value, $workerPermissions, true))
                                                    <span class="badge bg-light text-dark border">{{ $access->label() }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        @if ($canManageWorker)
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#worker-permissions-{{ $worker->id }}">
                                                Edytuj uprawnienia
                                            </button>
                                            @if ($workers->count() > 1)
                                                <form action="{{ route('manage.hotels.workers.destroy', [$hotel, $worker]) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Usunąć tego pracownika z hotelu?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Usuń</button>
                                                </form>
                                            @endif
                                        @elseif ($worker->id === $hotel->owner_id)
                                            <span class="small text-muted">Właściciel ma pełny dostęp</span>
                                        @else
                                            <span class="small text-muted">Brak uprawnień do edycji</span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($canManageWorker)
                                <tr class="collapse" id="worker-permissions-{{ $worker->id }}">
                                    <td colspan="3" class="bg-light">
                                        <form action="{{ route('manage.hotels.workers.update', [$hotel, $worker]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            @include('partials.worker-permissions-fields', [
                                                'accessOptions' => $accessOptions,
                                                'selected' => $workerPermissions,
                                                'inputIdPrefix' => 'worker-'.$worker->id,
                                            ])
                                            <button type="submit" class="btn btn-sm btn-primary mt-2">Zapisz uprawnienia</button>
                                        </form>
                                    </td>
                                </tr>
                                @endif
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
                    <form action="{{ route('manage.hotels.workers.store', $hotel) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="email">Adres e-mail</label>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="np. jan.kowalski@example.com"
                                   required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Użytkownik musi mieć już konto w systemie.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Uprawnienia</label>
                            @include('partials.worker-permissions-fields', [
                                'accessOptions' => $accessOptions,
                                'selected' => old('permissions', ['rooms', 'bookings', 'chat']),
                                'inputIdPrefix' => 'new-worker',
                            ])
                        </div>

                        <button type="submit" class="btn btn-primary">Dodaj pracownika</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
