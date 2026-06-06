<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Report $report): bool
    {
        return $user->id === $report->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function moderate(User $user, ?Report $report = null): bool
    {
        return in_array($user->email, config('maciej.admin_emails', []), true);
    }
}
