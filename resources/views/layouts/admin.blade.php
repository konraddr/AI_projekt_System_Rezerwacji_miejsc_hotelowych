@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row g-4">
            <aside class="col-12 col-md-3 col-lg-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-danger text-white fw-semibold">
                        Panel administratora
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('manage.admin.index') }}"
                           class="list-group-item list-group-item-action {{ request()->routeIs('manage.admin.index') ? 'active' : '' }}">
                            Przegląd
                        </a>
                        <a href="{{ route('manage.admin.reports.index') }}"
                           class="list-group-item list-group-item-action {{ request()->routeIs('manage.admin.reports.*') ? 'active' : '' }}">
                            Zgłoszenia
                        </a>
                        <a href="{{ route('manage.admin.hotels.index') }}"
                           class="list-group-item list-group-item-action {{ request()->routeIs('manage.admin.hotels.*') ? 'active' : '' }}">
                            Hotele
                        </a>
                        <a href="{{ route('manage.admin.users.index') }}"
                           class="list-group-item list-group-item-action {{ request()->routeIs('manage.admin.users.*') ? 'active' : '' }}">
                            Użytkownicy
                        </a>
                        <a href="{{ route('manage.admin.reviews.index') }}"
                           class="list-group-item list-group-item-action {{ request()->routeIs('manage.admin.reviews.*') ? 'active' : '' }}">
                            Moderacja opinii
                        </a>
                        <a href="{{ route('manage.amenities.index') }}"
                           class="list-group-item list-group-item-action {{ request()->routeIs('manage.amenities.*') ? 'active' : '' }}">
                            Udogodnienia
                        </a>
                    </div>
                </div>
            </aside>

            <div class="col-12 col-md-9 col-lg-10">
                @include('partials.alerts')
                @yield('admin-content')
            </div>
        </div>
    </div>
@endsection
