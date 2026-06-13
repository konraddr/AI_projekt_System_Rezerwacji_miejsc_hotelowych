<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateReportStatusRequest;
use App\Enums\ReportStatus;
use App\Models\Report;
use App\Services\ReportStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function __construct(
        private readonly ReportStatusService $reportStatusService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('moderate', Report::class);

        $reports = Report::query()
            ->with(['user', 'hotel', 'review.user'])
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.index', compact('reports'));
    }

    public function updateStatus(UpdateReportStatusRequest $request, Report $report): RedirectResponse
    {
        $this->authorize('updateStatus', $report);

        $this->reportStatusService->updateStatus(
            $report,
            $request->enum('status', ReportStatus::class)
        );

        $redirectParams = array_filter([
            'status' => $request->input('status_filter'),
        ]);

        return redirect()
            ->route('manage.admin.reports.index', $redirectParams)
            ->with('success', 'Status zgłoszenia #'.$report->id.' został zaktualizowany.');
    }
}
