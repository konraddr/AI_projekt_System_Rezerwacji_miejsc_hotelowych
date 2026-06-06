<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Report::class);

        $reports = Report::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('reports.index', compact('reports'));
    }

    public function create(): View
    {
        $this->authorize('create', Report::class);

        return view('reports.create');
    }

    public function store(StoreReportRequest $request): RedirectResponse
    {
        $this->authorize('create', Report::class);

        $report = Report::create([
            'user_id' => $request->user()->id,
            'title' => $request->validated('title'),
            'reason' => $request->validated('reason'),
        ]);

        return redirect()
            ->route('manage.reports.show', $report)
            ->with('success', 'Zgłoszenie zostało wysłane.');
    }

    public function show(Report $report): View
    {
        $this->authorize('view', $report);

        return view('reports.show', compact('report'));
    }
}
