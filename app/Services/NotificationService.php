<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;

class NotificationService
{
    /**
     * @return LengthAwarePaginator<int, DatabaseNotification>
     */
    public function paginatedForUser(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return $user->notifications()
            ->latest()
            ->paginate($perPage);
    }

    public function unreadCountForUser(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    public function markAsRead(User $user, string $notificationId): DatabaseNotification
    {
        /** @var DatabaseNotification $notification */
        $notification = $user->notifications()
            ->whereKey($notificationId)
            ->firstOrFail();

        if ($notification->read_at === null) {
            $notification->markAsRead();
            $notification->refresh();
        }

        return $notification;
    }

    public function markAllAsRead(User $user): int
    {
        $count = $user->unreadNotifications()->count();

        if ($count > 0) {
            $user->unreadNotifications->markAsRead();
        }

        return $count;
    }
}
