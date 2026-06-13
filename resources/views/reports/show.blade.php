@extends('layouts.app')

@section('title', 'Zgłoszenie #'.$report->id)

@section('content')
    <div class="container py-2">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manage.reports.index') }}">Zgłoszenia</a></li>
                <li class="breadcrumb-item active" aria-current="page">#{{ $report->id }}</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                @include('partials.alerts')

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h1 class="h4 mb-0">Zgłoszenie #{{ $report->id }}</h1>
                        <span class="badge bg-secondary">{{ $report->status->label() }}</span>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Typ</dt>
                            <dd class="col-sm-9">{{ str_replace('_', ' ', $report->title->value) }}</dd>

                            @if ($report->hotel)
                                <dt class="col-sm-3">Hotel</dt>
                                <dd class="col-sm-9">
                                    <a href="{{ route('hotels.show', $report->hotel) }}">{{ $report->hotel->name }}</a>
                                </dd>
                            @endif

                            <dt class="col-sm-3">Data</dt>
                            <dd class="col-sm-9">{{ $report->created_at->format('d.m.Y H:i') }}</dd>

                            <dt class="col-sm-3">Status</dt>
                            <dd class="col-sm-9">{{ $report->status->label() }}</dd>

                            <dt class="col-sm-3">Opis</dt>
                            <dd class="col-sm-9">{{ $report->reason }}</dd>
                        </dl>
                    </div>
                    <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <a href="{{ route('manage.reports.index') }}" class="btn btn-outline-secondary">Powrót do listy</a>

                        @can('updateStatus', $report)
                            <form action="{{ route('manage.reports.update-status', $report) }}" method="POST"
                                  class="d-inline-flex align-items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <label class="small fw-semibold mb-0" for="status">Zmień status:</label>
                                <select name="status" id="status" class="form-select form-select-sm" style="min-width: 9rem;">
                                    @foreach (\App\Enums\ReportStatus::cases() as $statusCase)
                                        <option value="{{ $statusCase->value }}" @selected($report->status === $statusCase)>
                                            {{ $statusCase->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Zapisz</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
