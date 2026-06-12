<?php

namespace App\Services;

use App\Enums\HotelWorkerAccess;
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

    public function userCanAccess(User $user, Hotel $hotel, HotelWorkerAccess $access): bool
    {
        if ($user->hasPermission(UserPermission::Administrator)) {
            return true;
        }

        return in_array($access->value, $this->workerPermissions($user, $hotel), true);
    }

    public function authorizeHotelCapability(User $user, Hotel $hotel, HotelWorkerAccess $access): void
    {
        abort_unless($this->userCanAccess($user, $hotel, $access), 403);
    }

    /**
     * @return list<string>
     */
    public function workerPermissions(User $user, Hotel $hotel): array
    {
        if ($user->hasPermission(UserPermission::Administrator)) {
            return HotelWorkerAccess::values();
        }

        $assignment = $user->workerHotels()->whereKey($hotel->id)->first();

        if ($assignment === null) {
            return [];
        }

        $permissions = $assignment->pivot->permissions;

        if (! is_array($permissions) || $permissions === []) {
            return HotelWorkerAccess::values();
        }

        return array_values(array_intersect($permissions, HotelWorkerAccess::values()));
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
