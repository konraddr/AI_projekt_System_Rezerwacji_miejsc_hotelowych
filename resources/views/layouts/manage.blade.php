@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row g-4">
            <aside class="col-12 col-md-3 col-lg-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white fw-semibold">
                        Panel właściciela
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('manage.hotels.index') }}"
                           class="list-group-item list-group-item-action {{ request()->routeIs('manage.hotels.index') ? 'active' : '' }}">
                            Moje hotele
                        </a>
                        <a href="{{ route('manage.hotels.create') }}"
                           class="list-group-item list-group-item-action {{ request()->routeIs('manage.hotels.create') ? 'active' : '' }}">
                            Dodaj hotel
                        </a>
                        @if (auth()->user()->hasPermission(\App\Enums\UserPermission::Administrator))
                            <a href="{{ route('manage.amenities.index') }}"
                               class="list-group-item list-group-item-action {{ request()->routeIs('manage.amenities.*') ? 'active' : '' }}">
                                Udogodnienia
                            </a>
                        @endif
                        <a href="{{ route('hotels.index') }}" class="list-group-item list-group-item-action">
                            Katalog publiczny
                        </a>
                    </div>
                </div>
            </aside>

            <div class="col-12 col-md-9 col-lg-10">
                @include('partials.alerts')
                @yield('manage-content')
            </div>
        </div>
    </div>
@endsection
