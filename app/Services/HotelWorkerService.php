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
    public function __construct(
        private readonly HotelAccessService $hotelAccess
    ) {}

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
    public function normalizePermissions(array $permissions, bool $grantWorkerManagement = false): array
    {
        $allowed = HotelWorkerAccess::values();

        if (! $grantWorkerManagement) {
            $allowed = array_values(array_filter(
                $allowed,
                fn (string $permission) => $permission !== HotelWorkerAccess::Workers->value
            ));
        }

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
     * @return list<string>
     */
    public function permissionsForActor(Hotel $hotel, User $actor, array $permissions): array
    {
        $normalized = $this->normalizePermissions(
            $permissions,
            $this->hotelAccess->canAssignWorkerManagementPermission($actor, $hotel)
        );

        $assignable = $this->hotelAccess->assignableWorkerPermissionsFor($actor, $hotel);
        $constrained = array_values(array_intersect($normalized, $assignable));

        if ($constrained === []) {
            throw new InvalidArgumentException('Nie możesz nadać wybranych uprawnień.');
        }

        return $constrained;
    }

    /**
     * @param  list<string>  $permissions
     */
    public function attachWorker(Hotel $hotel, User $worker, array $permissions, User $actor): void
    {
        if ($worker->isBanned()) {
            throw new InvalidArgumentException('Nie można dodać zablokowanego użytkownika.');
        }

        if ($hotel->owner_id === $worker->id) {
            throw new InvalidArgumentException('Właściciel hotelu jest już przypisany do tego obiektu.');
        }

        if ($hotel->workers()->whereKey($worker->id)->exists()) {
            throw new InvalidArgumentException('Ten użytkownik jest już pracownikiem hotelu.');
        }

        $hotel->workers()->attach($worker->id, [
            'permissions' => $this->permissionsForActor($hotel, $actor, $permissions),
        ]);

        if ($worker->hasPermission(UserPermission::Client)) {
            $worker->update(['permission' => UserPermission::Worker]);
        }
    }

    /**
     * @param  list<string>  $permissions
     */
    public function attachWorkerByEmail(Hotel $hotel, string $email, array $permissions, User $actor): User
    {
        $worker = User::query()
            ->where('email', Str::lower(trim($email)))
            ->first();

        if ($worker === null) {
            throw new InvalidArgumentException('Nie znaleziono użytkownika o podanym adresie e-mail.');
        }

        $this->attachWorker($hotel, $worker, $permissions, $actor);

        return $worker;
    }

    /**
     * @param  list<string>  $permissions
     */
    public function updateWorkerPermissions(Hotel $hotel, User $worker, array $permissions, User $actor): void
    {
        if (! $hotel->workers()->whereKey($worker->id)->exists()) {
            throw new InvalidArgumentException('Ten użytkownik nie jest pracownikiem hotelu.');
        }

        if ($hotel->owner_id === $worker->id) {
            throw new InvalidArgumentException('Nie można zmieniać uprawnień właściciela hotelu.');
        }

        if (
            $worker->id === $actor->id
            && ! $this->hotelAccess->userIsHotelOwner($actor, $hotel)
            && ! $actor->hasPermission(UserPermission::Administrator)
        ) {
            throw new InvalidArgumentException('Nie możesz zmieniać własnych uprawnień.');
        }

        $hotel->workers()->updateExistingPivot($worker->id, [
            'permissions' => $this->permissionsForActor($hotel, $actor, $permissions),
        ]);
    }

    public function detachWorker(Hotel $hotel, User $worker, User $actor): void
    {
        if (! $hotel->workers()->whereKey($worker->id)->exists()) {
            throw new InvalidArgumentException('Ten użytkownik nie jest pracownikiem hotelu.');
        }

        if ($hotel->owner_id === $worker->id) {
            throw new InvalidArgumentException('Nie można usunąć właściciela hotelu.');
        }

        if (
            $worker->id === $actor->id
            && ! $this->hotelAccess->userIsHotelOwner($actor, $hotel)
            && ! $actor->hasPermission(UserPermission::Administrator)
        ) {
            throw new InvalidArgumentException('Nie możesz usunąć samego siebie z hotelu.');
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
