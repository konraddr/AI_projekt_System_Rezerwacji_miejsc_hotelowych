<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\Report;

class ReportStatusService
{
    public function __construct(
        private readonly ReportNotificationService $reportNotificationService
    ) {}

    public function updateStatus(Report $report, ReportStatus $status): bool
    {
        if ($report->status === $status) {
            return false;
        }

        $report->update(['status' => $status]);
        $report->refresh();

        $this->reportNotificationService->notifyReporterStatusChanged($report);

        return true;
    }
}
