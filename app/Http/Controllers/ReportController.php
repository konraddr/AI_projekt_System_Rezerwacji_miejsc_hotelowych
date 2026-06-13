<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportStatusRequest;
use App\Enums\ReportStatus;
use App\Models\Hotel;
use App\Models\Report;
use App\Services\ReportNotificationService;
use App\Services\ReportStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportNotificationService $reportNotificationService,
        private readonly ReportStatusService $reportStatusService
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Report::class);

        $reports = Report::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('reports.index', compact('reports'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Report::class);

        $hotel = null;
        if ($request->filled('hotel')) {
            $hotel = Hotel::query()->find($request->query('hotel'));
        }

        return view('reports.create', compact('hotel'));
    }

    public function store(StoreReportRequest $request): RedirectResponse
    {
        $this->authorize('create', Report::class);

        $report = Report::create([
            'user_id' => $request->user()->id,
            'hotel_id' => $request->validated('hotel_id'),
            'title' => $request->validated('title'),
            'reason' => $request->validated('reason'),
        ]);

        $this->reportNotificationService->notifyResponsibleUsers($report);
        $this->reportNotificationService->notifyReporterSubmitted($report);

        return redirect()
            ->route('manage.reports.show', $report)
            ->with('success', 'Zgłoszenie zostało wysłane.');
    }

    public function show(Report $report): View
    {
        $this->authorize('view', $report);

        $report->load('hotel');

        return view('reports.show', compact('report'));
    }

    public function updateStatus(UpdateReportStatusRequest $request, Report $report): RedirectResponse
    {
        $this->authorize('updateStatus', $report);

        $this->reportStatusService->updateStatus(
            $report,
            $request->enum('status', ReportStatus::class)
        );

        return redirect()
            ->route('manage.reports.show', $report)
            ->with('success', 'Status zgłoszenia został zaktualizowany.');
    }
}
