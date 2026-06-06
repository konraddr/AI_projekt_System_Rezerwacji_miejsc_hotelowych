@extends('layouts.app')

@section('title', 'Moje zgłoszenia')

@section('content')
    <div class="container py-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 mb-1">Moje zgłoszenia</h1>
                <p class="text-muted mb-0">Historia zgłoszeń do administratora.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('manage.reports.create') }}" class="btn btn-primary">Nowe zgłoszenie</a>
                @if (in_array(auth()->user()->email, config('maciej.admin_emails', []), true))
                    <a href="{{ route('manage.admin.reports.index') }}" class="btn btn-outline-danger">Panel zgłoszeń</a>
                    <a href="{{ route('manage.admin.reviews.index') }}" class="btn btn-outline-secondary">Moderacja opinii</a>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush">
                @forelse ($reports as $report)
                    <a href="{{ route('manage.reports.show', $report) }}"
                       class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ str_replace('_', ' ', $report->title->value) }}</strong>
                                <p class="mb-0 small text-muted">{{ Str::limit($report->reason, 100) }}</p>
                            </div>
                            <span class="badge bg-secondary">{{ $report->status->value }}</span>
                        </div>
                        <small class="text-muted">{{ $report->created_at->format('d.m.Y H:i') }}</small>
                    </a>
                @empty
                    <div class="list-group-item text-center text-muted py-5">
                        <p class="mb-3">Nie masz jeszcze żadnych zgłoszeń.</p>
                        <a href="{{ route('manage.reports.create') }}" class="btn btn-primary btn-sm">Utwórz zgłoszenie</a>
                    </div>
                @endforelse
            </div>
            @if ($reports->hasPages())
                <div class="card-footer bg-white">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
