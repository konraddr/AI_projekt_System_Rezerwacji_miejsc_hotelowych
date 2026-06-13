<?php

namespace App\Http\Controllers;

use App\Enums\ReportTitle;
use App\Http\Requests\StoreReviewReportRequest;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\Report;
use App\Services\ReportNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class ReviewReportController extends Controller
{
    public function __construct(
        private readonly ReportNotificationService $reportNotificationService
    ) {}

    public function store(StoreReviewReportRequest $request, Hotel $hotel, Review $review): RedirectResponse
    {
        abort_if($review->hotel_id !== $hotel->id, 404);

        $review->loadMissing(['user', 'hotel']);

        $report = Report::create([
            'user_id' => $request->user()?->id,
            'hotel_id' => $hotel->id,
            'review_id' => $review->id,
            'title' => ReportTitle::ToksycznyKomentarz,
            'reason' => $this->buildReason($review, $request->user()?->name),
        ]);

        $this->reportNotificationService->notifyResponsibleUsers($report);

        if ($request->user() !== null) {
            $this->reportNotificationService->notifyReporterSubmitted($report);
        }

        return redirect()
            ->route('hotels.show', $hotel)
            ->with('success', 'Opinia została zgłoszona administratorowi.');
    }

    private function buildReason(Review $review, ?string $reporterName): string
    {
        $reporter = $reporterName ?? 'Gość';
        $author = $review->user->name;
        $excerpt = Str::limit($review->comment, 200);

        return "Zgłoszenie opinii #{$review->id} w hotelu {$review->hotel->name}. "
            ."Zgłaszający: {$reporter}. Autor opinii: {$author}. "
            ."Ocena: {$review->rating}/5. Komentarz: „{$excerpt}”";
    }
}
