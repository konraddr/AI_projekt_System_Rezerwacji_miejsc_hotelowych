<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Notifications\ReportStatusChangedNotification;
use App\Notifications\ReportSubmittedNotification;
use App\Notifications\NewReportNotification;
use Throwable;

class ReportNotificationService
{
    public function __construct(
        private readonly ReportRecipientService $reportRecipientService
    ) {}

    public function notifyResponsibleUsers(Report $report): void
    {
        $report->loadMissing(['user', 'hotel']);

        foreach ($this->reportRecipientService->recipientsForReport($report) as $recipient) {
            try {
                $recipient->notify(new NewReportNotification($report));
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }

    public function notifyReporterSubmitted(Report $report): void
    {
        $report->loadMissing(['user', 'hotel']);

        if ($report->user === null || $report->user->isBanned()) {
            return;
        }

        try {
            $report->user->notify(new ReportSubmittedNotification($report));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function notifyReporterStatusChanged(Report $report): void
    {
        $report->loadMissing(['user', 'hotel']);

        if ($report->user === null || $report->user->isBanned()) {
            return;
        }

        try {
            $report->user->notify(new ReportStatusChangedNotification($report));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
