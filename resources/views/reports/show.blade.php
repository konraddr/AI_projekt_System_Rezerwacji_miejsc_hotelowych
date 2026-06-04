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
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h1 class="h4 mb-0">Zgłoszenie #{{ $report->id }}</h1>
                        <span class="badge bg-secondary">{{ $report->status->value }}</span>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Typ</dt>
                            <dd class="col-sm-9">{{ str_replace('_', ' ', $report->title->value) }}</dd>

                            <dt class="col-sm-3">Data</dt>
                            <dd class="col-sm-9">{{ $report->created_at->format('d.m.Y H:i') }}</dd>

                            <dt class="col-sm-3">Status</dt>
                            <dd class="col-sm-9">{{ $report->status->value }}</dd>

                            <dt class="col-sm-3">Opis</dt>
                            <dd class="col-sm-9">{{ $report->reason }}</dd>
                        </dl>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('manage.reports.index') }}" class="btn btn-outline-secondary">Powrót do listy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
