@extends('layouts.admin')

@section('title', 'Panel administratora')

@section('admin-content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Panel administratora</h1>
        <p class="text-muted mb-0">Zarządzanie zgłoszeniami, hotelami i użytkownikami systemu.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Zgłoszenia oczekujące</h2>
                    <p class="display-6 mb-3">{{ $pendingReportsCount }}</p>
                    <a href="{{ route('manage.admin.reports.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-danger">
                        Obsłuż zgłoszenia
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Hotele</h2>
                    <p class="display-6 mb-3">{{ $hotelsCount }}</p>
                    <a href="{{ route('manage.admin.hotels.index') }}" class="btn btn-sm btn-outline-primary">
                        Zarządzaj hotelami
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Użytkownicy</h2>
                    <p class="display-6 mb-3">{{ $usersCount }}</p>
                    <a href="{{ route('manage.admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                        Zarządzaj użytkownikami
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
