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
        if ($access === HotelWorkerAccess::Workers) {
            return $this->userCanManageWorkerRoles($user, $hotel);
        }

        if ($user->hasPermission(UserPermission::Administrator) || $this->userIsHotelOwner($user, $hotel)) {
            return true;
        }

        return in_array($access->value, $this->workerPermissions($user, $hotel), true);
    }

    public function userCanUseHotelChat(User $user, Hotel $hotel): bool
    {
        if ($user->isBanned()) {
            return false;
        }

        if ($user->hasPermission(UserPermission::Administrator) || $this->userIsHotelOwner($user, $hotel)) {
            return true;
        }

        if (! $this->userCanManageHotel($user, $hotel)) {
            return true;
        }

        return $this->userCanAccess($user, $hotel, HotelWorkerAccess::Chat);
    }

    public function authorizeHotelCapability(User $user, Hotel $hotel, HotelWorkerAccess $access): void
    {
        abort_unless($this->userCanAccess($user, $hotel, $access), 403);
    }

    public function userIsHotelOwner(User $user, Hotel $hotel): bool
    {
        return $hotel->owner_id === $user->id;
    }

    public function userCanManageWorkerRoles(User $user, Hotel $hotel): bool
    {
        if ($user->hasPermission(UserPermission::Administrator) || $this->userIsHotelOwner($user, $hotel)) {
            return true;
        }

        return in_array(HotelWorkerAccess::Workers->value, $this->pivotPermissions($user, $hotel), true);
    }

    public function authorizeWorkerRoleManagement(User $user, Hotel $hotel): void
    {
        abort_unless($this->userCanManageWorkerRoles($user, $hotel), 403);
    }

    public function canAssignWorkerManagementPermission(User $user, Hotel $hotel): bool
    {
        return in_array(
            HotelWorkerAccess::Workers->value,
            $this->assignableWorkerPermissionsFor($user, $hotel),
            true
        );
    }

    /**
     * @return list<string>
     */
    public function assignableWorkerPermissionsFor(User $actor, Hotel $hotel): array
    {
        if ($actor->hasPermission(UserPermission::Administrator) || $this->userIsHotelOwner($actor, $hotel)) {
            return HotelWorkerAccess::values();
        }

        if (! $this->userCanManageWorkerRoles($actor, $hotel)) {
            return [];
        }

        return $this->pivotPermissions($actor, $hotel);
    }

    /**
     * @return list<string>
     */
    public function pivotPermissions(User $user, Hotel $hotel): array
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
            return [];
        }

        return array_values(array_intersect($permissions, HotelWorkerAccess::values()));
    }

    /**
     * @return list<string>
     */
    public function workerPermissions(User $user, Hotel $hotel): array
    {
        if ($user->hasPermission(UserPermission::Administrator) || $this->userIsHotelOwner($user, $hotel)) {
            return HotelWorkerAccess::values();
        }

        return array_values(array_filter(
            $this->pivotPermissions($user, $hotel),
            fn (string $permission) => $permission !== HotelWorkerAccess::Workers->value
        ));
    }

    /**
     * @return array{bookings: bool, workers: bool, rooms: bool, photos: bool, chat: bool, hotel: bool}
     */
    public function ownerPanelLinks(User $user, Hotel $hotel): array
    {
        return [
            'bookings' => $this->userCanAccess($user, $hotel, HotelWorkerAccess::Bookings),
            'workers' => $this->userCanManageWorkerRoles($user, $hotel),
            'rooms' => $this->userCanAccess($user, $hotel, HotelWorkerAccess::Rooms),
            'photos' => $this->userCanAccess($user, $hotel, HotelWorkerAccess::Photos),
            'chat' => $this->userCanAccess($user, $hotel, HotelWorkerAccess::Chat),
            'hotel' => $this->userCanAccess($user, $hotel, HotelWorkerAccess::Hotel),
        ];
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
