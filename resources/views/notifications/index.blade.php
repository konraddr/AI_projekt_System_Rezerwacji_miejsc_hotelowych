@extends('layouts.app')

@section('title', 'Powiadomienia')

@section('content')
    <div class="container py-2">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <h1 class="h3 fw-bold mb-0">Powiadomienia</h1>
            @if (auth()->user()->unreadNotifications->isNotEmpty())
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                        Oznacz wszystkie jako przeczytane
                    </button>
                </form>
            @endif
        </div>

        @include('partials.alerts')

        @forelse ($notifications as $notification)
            <div class="card shadow-sm border-0 mb-3 @if ($notification->read_at === null) border-start border-primary border-4 @endif">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                        <h2 class="h6 fw-bold mb-0">{{ $notification->data['title'] ?? 'Powiadomienie' }}</h2>
                        <span class="text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mb-3">{{ $notification->data['message'] ?? '' }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        @if (! empty($notification->data['url']))
                            @if ($notification->read_at === null)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        {{ $notification->data['action_label'] ?? 'Przejdź do szczegółów' }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-outline-primary">
                                    {{ $notification->data['action_label'] ?? 'Zobacz szczegóły' }}
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5 text-muted">
                    <p class="mb-0">Brak powiadomień.</p>
                </div>
            </div>
        @endforelse

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    </div>
@endsection
