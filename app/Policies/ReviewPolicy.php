<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Review $review): bool
    {
        return ! $review->is_banned || ($user !== null && $user->id === $review->user_id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    public function moderate(User $user, ?Review $review = null): bool
    {
        return $user->hasPermission(UserPermission::Administrator)
            || in_array($user->email, config('maciej.admin_emails', []), true);
    }
}
