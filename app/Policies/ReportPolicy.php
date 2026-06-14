<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\Report;
use App\Models\User;
use App\Services\ReportRecipientService;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Report $report): bool
    {
        if ($report->user_id !== null && $user->id === $report->user_id) {
            return true;
        }

        return app(ReportRecipientService::class)->userCanHandleReport($user, $report);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function moderate(User $user, ?Report $report = null): bool
    {
        return $user->hasPermission(UserPermission::Administrator)
            || in_array($user->email, config('maciej.admin_emails', []), true);
    }

    public function updateStatus(User $user, Report $report): bool
    {
        return $this->moderate($user, $report);
    }
}
