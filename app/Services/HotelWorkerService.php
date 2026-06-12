<?php

namespace App\Services;

use App\Enums\HotelWorkerAccess;
use App\Enums\UserPermission;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
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
     * @param  list<string>  $permissions
     * @return list<string>
     */
    public function normalizePermissions(array $permissions): array
    {
        $allowed = HotelWorkerAccess::values();

        $normalized = collect($permissions)
            ->map(fn (mixed $permission) => is_string($permission) ? Str::lower(trim($permission)) : null)
            ->filter(fn (?string $permission) => $permission !== null && in_array($permission, $allowed, true))
            ->unique()
            ->values()
            ->all();

        if ($normalized === []) {
            throw new InvalidArgumentException('Wybierz co najmniej jedno uprawnienie dla pracownika.');
        }

        return $normalized;
    }

    /**
     * @param  list<string>  $permissions
     */
    public function attachWorker(Hotel $hotel, User $worker, array $permissions): void
    {
        if ($worker->isBanned()) {
            throw new InvalidArgumentException('Nie można dodać zablokowanego użytkownika.');
        }

        if ($hotel->workers()->whereKey($worker->id)->exists()) {
            throw new InvalidArgumentException('Ten użytkownik jest już pracownikiem hotelu.');
        }

        $hotel->workers()->attach($worker->id, [
            'permissions' => $this->normalizePermissions($permissions),
        ]);

        if ($worker->hasPermission(UserPermission::Client)) {
            $worker->update(['permission' => UserPermission::Worker]);
        }
    }

    /**
     * @param  list<string>  $permissions
     */
    public function attachWorkerByEmail(Hotel $hotel, string $email, array $permissions): User
    {
        $worker = User::query()
            ->where('email', Str::lower(trim($email)))
            ->first();

        if ($worker === null) {
            throw new InvalidArgumentException('Nie znaleziono użytkownika o podanym adresie e-mail.');
        }

        $this->attachWorker($hotel, $worker, $permissions);

        return $worker;
    }

    /**
     * @param  list<string>  $permissions
     */
    public function updateWorkerPermissions(Hotel $hotel, User $worker, array $permissions): void
    {
        if (! $hotel->workers()->whereKey($worker->id)->exists()) {
            throw new InvalidArgumentException('Ten użytkownik nie jest pracownikiem hotelu.');
        }

        $hotel->workers()->updateExistingPivot($worker->id, [
            'permissions' => $this->normalizePermissions($permissions),
        ]);
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
