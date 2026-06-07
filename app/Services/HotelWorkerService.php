<?php

namespace App\Services;

use App\Enums\UserPermission;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class HotelWorkerService
{
    /**
     * @return Collection<int, User>
     */
    public function workersForHotel(Hotel $hotel): Collection
    {
        return $hotel->workers()->orderBy('name')->get();
    }

    /**
     * @return Collection<int, User>
     */
    public function assignableUsers(Hotel $hotel): Collection
    {
        $workerIds = $hotel->workers()->pluck('users.id');

        return User::query()
            ->where('permission', '!=', UserPermission::Banned)
            ->whereNotIn('id', $workerIds)
            ->orderBy('name')
            ->get();
    }

    public function attachWorker(Hotel $hotel, User $worker): void
    {
        if ($worker->isBanned()) {
            throw new InvalidArgumentException('Nie można dodać zablokowanego użytkownika.');
        }

        if ($hotel->workers()->whereKey($worker->id)->exists()) {
            throw new InvalidArgumentException('Ten użytkownik jest już pracownikiem hotelu.');
        }

        $hotel->workers()->attach($worker->id);

        if ($worker->hasPermission(UserPermission::Client)) {
            $worker->update(['permission' => UserPermission::Worker]);
        }
    }

    public function detachWorker(Hotel $hotel, User $worker): void
    {
        if (! $hotel->workers()->whereKey($worker->id)->exists()) {
            throw new InvalidArgumentException('Ten użytkownik nie jest pracownikiem hotelu.');
        }

        if ($hotel->workers()->count() <= 1) {
            throw new InvalidArgumentException('Hotel musi mieć co najmniej jednego pracownika.');
        }

        $hotel->workers()->detach($worker->id);

        if ($worker->workerHotels()->doesntExist() && $worker->hasPermission(UserPermission::Worker)) {
            $worker->update(['permission' => UserPermission::Client]);
        }
    }
}
