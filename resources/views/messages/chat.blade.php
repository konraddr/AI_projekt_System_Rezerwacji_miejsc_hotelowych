@extends('layouts.app')

@section('title', 'Czat — '.$hotel->name)

@section('content')
    <div class="container py-2">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hotels.index') }}">Katalog</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hotels.show', $hotel) }}">{{ $hotel->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Czat</li>
            </ol>
        </nav>

        <div class="card border-0 shadow-sm" id="hotel-chat"
             data-poll-url="{{ route('manage.hotels.messages.index', $hotel) }}"
             data-store-url="{{ route('manage.hotels.messages.store', $hotel) }}"
             data-poll-interval="60000">
            <div class="card-header bg-white">
                <h1 class="h4 mb-1">Czat: {{ $hotel->name }}</h1>
                <p class="text-muted small mb-0">Nowe wiadomości są pobierane co 60 sekund (polling).</p>
            </div>

            <div class="card-body p-0">
                <div id="chat-messages" class="p-3" style="min-height: 280px; max-height: 420px; overflow-y: auto;">
                    <p class="text-muted mb-0" id="chat-loading">Ładowanie wiadomości…</p>
                </div>
            </div>

            <div class="card-footer bg-white">
                <form id="chat-form" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold" for="receiver_id">Odbiorca</label>
                        <select name="receiver_id" id="receiver_id" class="form-select" required>
                            @foreach ($receivers as $receiver)
                                <option value="{{ $receiver->id }}" @selected($receiver->id === $defaultReceiverId)>
                                    {{ $receiver->name }}
                                    @if ($receiver->id === auth()->id())
                                        (ja — test)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small fw-semibold" for="content">Wiadomość</label>
                        <textarea name="content" id="content" class="form-control" rows="2" maxlength="2000"
                                  placeholder="Napisz wiadomość…" required></textarea>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100" id="chat-submit">Wyślij</button>
                    </div>
                </form>
                <div id="chat-error" class="alert alert-danger mt-2 mb-0 d-none" role="alert"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/hotel-chat-polling.js') }}" defer></script>
@endpush
