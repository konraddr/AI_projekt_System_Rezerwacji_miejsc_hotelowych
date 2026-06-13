<?php

namespace App\Services;

use App\Enums\HotelWorkerAccess;
use App\Enums\UserPermission;
use App\Models\Hotel;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ReportRecipientService
{
    public function __construct(
        private readonly HotelAccessService $hotelAccess
    ) {}

    /**
     * @return Collection<int, User>
     */
    public function recipientsForReport(Report $report): Collection
    {
        if ($report->review_id !== null) {
            return new Collection;
        }

        $recipients = new Collection;

        if ($report->hotel_id !== null) {
            $hotel = $report->relationLoaded('hotel')
                ? $report->hotel
                : Hotel::query()->find($report->hotel_id);

            if ($hotel !== null) {
                $hotel->load('workers');

                foreach ($hotel->workers as $worker) {
                    if ($this->hotelAccess->userCanAccess($worker, $hotel, HotelWorkerAccess::Hotel)
                        || $this->hotelAccess->userCanAccess($worker, $hotel, HotelWorkerAccess::Chat)) {
                        $recipients->push($worker);
                    }
                }
            }
        }

        return $recipients
            ->unique('id')
            ->filter(fn (User $user) => $user->id !== $report->user_id)
            ->filter(fn (User $user) => ! $user->isBanned())
            ->values();
    }

    public function userCanHandleReport(User $user, Report $report): bool
    {
        if ($user->hasPermission(UserPermission::Administrator)
            || in_array($user->email, config('maciej.admin_emails', []), true)) {
            return true;
        }

        if ($report->hotel_id === null) {
            return false;
        }

        $hotel = $report->relationLoaded('hotel')
            ? $report->hotel
            : Hotel::query()->find($report->hotel_id);

        if ($hotel === null) {
            return false;
        }

        return $this->hotelAccess->userCanAccess($user, $hotel, HotelWorkerAccess::Hotel)
            || $this->hotelAccess->userCanAccess($user, $hotel, HotelWorkerAccess::Chat);
    }

    /**
     * @return Collection<int, User>
     */
    public function administratorsOnly(): Collection
    {
        $recipients = User::query()
            ->where('permission', UserPermission::Administrator)
            ->get();

        $adminEmails = config('maciej.admin_emails', []);
        if ($adminEmails !== []) {
            $recipients = $recipients->merge(
                User::query()->whereIn('email', $adminEmails)->get()
            );
        }

        return $recipients
            ->unique('id')
            ->filter(fn (User $user) => ! $user->isBanned())
            ->values();
    }
}
