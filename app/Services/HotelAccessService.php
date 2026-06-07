<?php

namespace App\Services;

use App\Enums\UserPermission;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class HotelAccessService
{
    public function userCanManageHotel(User $user, Hotel $hotel): bool
    {
        if ($user->hasPermission(UserPermission::Administrator)) {
            return true;
        }

        return $user->workerHotels()->whereKey($hotel->id)->exists();
    }

    public function authorizeHotelAccess(User $user, Hotel $hotel): void
    {
        abort_unless($this->userCanManageHotel($user, $hotel), 403);
    }

    /**
     * @return Collection<int, Hotel>
     */
    public function hotelsForUser(User $user): Collection
    {
        $query = Hotel::query()
            ->withCount(['rooms', 'amenities'])
            ->latest();

        if ($user->hasPermission(UserPermission::Administrator)) {
            return $query->get();
        }

        return $query
            ->whereHas('workers', fn ($workers) => $workers->whereKey($user->id))
            ->get();
    }
}
