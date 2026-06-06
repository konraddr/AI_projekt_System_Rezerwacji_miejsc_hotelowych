<?php

namespace App\Console\Commands;

use App\Services\BookingPenaltyService;
use Illuminate\Console\Command;

class PenalizeUnpaidBookings extends Command
{
    protected $signature = 'bookings:penalize-unpaid';

    protected $description = 'Anuluje przeterminowane nieopłacone rezerwacje i blokuje konta klientów';

    public function handle(BookingPenaltyService $penaltyService): int
    {
        $graceHours = config('bookings.unpaid_grace_hours');
        $penalizedCount = $penaltyService->penalizeOverdueUnpaidBookings();

        if ($penalizedCount === 0) {
            $this->info("Brak rezerwacji oczekujących na wpłatę dłużej niż {$graceHours} h.");

            return self::SUCCESS;
        }

        $this->info("Ukaroń {$penalizedCount} przeterminowanych rezerwacji (limit: {$graceHours} h).");

        return self::SUCCESS;
    }
}
