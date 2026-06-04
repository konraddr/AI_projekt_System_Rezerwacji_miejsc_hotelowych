@extends('layouts.manage')

@section('title', 'Panel zgłoszeń')

@section('manage-content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Panel zgłoszeń</h1>
            <p class="text-muted mb-0">Zmiana statusu zgłoszeń użytkowników (pending / resolved / rejected).</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('manage.admin.reviews.index') }}" class="btn btn-outline-secondary">Moderacja opinii</a>
            <a href="{{ route('manage.reports.index') }}" class="btn btn-outline-primary">Moje zgłoszenia</a>
        </div>
    </div>

    <div class="btn-group mb-4 flex-wrap" role="group">
        <a href="{{ route('manage.admin.reports.index') }}"
           class="btn btn-sm {{ ! request('status') ? 'btn-primary' : 'btn-outline-primary' }}">Wszystkie</a>
        @foreach (\App\Enums\ReportStatus::cases() as $statusCase)
            <a href="{{ route('manage.admin.reports.index', ['status' => $statusCase->value]) }}"
               class="btn btn-sm {{ request('status') === $statusCase->value ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $statusCase->label() }}
            </a>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Użytkownik</th>
                        <th>Typ</th>
                        <th>Opis</th>
                        <th>Data</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Akcja</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->user->name }}</td>
                            <td>{{ str_replace('_', ' ', $report->title->value) }}</td>
                            <td>{{ Str::limit($report->reason, 60) }}</td>
                            <td class="text-nowrap small text-muted">{{ $report->created_at->format('d.m.Y H:i') }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $report->status->label() }}</span>
                            </td>
                            <td class="text-end">
                                <form action="{{ route('manage.admin.reports.update-status', $report) }}" method="POST"
                                      class="d-inline-flex align-items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    @if (request('status'))
                                        <input type="hidden" name="status_filter" value="{{ request('status') }}">
                                    @endif
                                    <select name="status" class="form-select form-select-sm" style="min-width: 9rem;">
                                        @foreach (\App\Enums\ReportStatus::cases() as $statusCase)
                                            <option value="{{ $statusCase->value }}" @selected($report->status === $statusCase)>
                                                {{ $statusCase->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Zapisz</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Brak zgłoszeń do obsługi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($reports->hasPages())
            <div class="card-footer bg-white">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
@endsection
