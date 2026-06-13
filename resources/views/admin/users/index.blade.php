@extends('layouts.admin')

@section('title', 'Zarządzanie użytkownikami')

@section('admin-content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Użytkownicy</h1>
            <p class="text-muted mb-0">Pełne zarządzanie kontami użytkowników systemu.</p>
        </div>
        <a href="{{ route('manage.admin.users.create') }}" class="btn btn-primary">Dodaj użytkownika</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Imię i nazwisko</th>
                        <th>E-mail</th>
                        <th>Telefon</th>
                        <th>Uprawnienie</th>
                        <th>Data rejestracji</th>
                        <th class="text-end">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="{{ $user->isBanned() ? 'table-warning' : '' }}">
                            <td>{{ trim($user->name.' '.($user->last_name ?? '')) }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '—' }}</td>
                            <td>{{ $user->permission->label() }}</td>
                            <td class="text-nowrap small text-muted">{{ $user->created_at->format('d.m.Y H:i') }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('manage.admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning">Edytuj</a>
                                    @if ($user->id !== auth()->id())
                                        @include('partials.delete-modal', [
                                            'modalId' => 'deleteUser'.$user->id,
                                            'title' => 'Usuń użytkownika',
                                            'message' => 'Czy na pewno chcesz usunąć użytkownika „'.$user->email.'”? Tej operacji nie można cofnąć.',
                                            'action' => route('manage.admin.users.destroy', $user),
                                        ])
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">Brak użytkowników.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="card-footer bg-white">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
