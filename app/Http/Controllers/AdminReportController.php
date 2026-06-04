<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateReportStatusRequest;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('moderate', Report::class);

        $reports = Report::query()
            ->with('user')
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.index', compact('reports'));
    }

    public function updateStatus(UpdateReportStatusRequest $request, Report $report): RedirectResponse
    {
        $this->authorize('moderate', Report::class);

        $report->update(['status' => $request->validated('status')]);

        $redirectParams = array_filter([
            'status' => $request->input('status_filter'),
        ]);

        return redirect()
            ->route('manage.admin.reports.index', $redirectParams)
            ->with('success', 'Status zgłoszenia #'.$report->id.' został zaktualizowany.');
    }
}
