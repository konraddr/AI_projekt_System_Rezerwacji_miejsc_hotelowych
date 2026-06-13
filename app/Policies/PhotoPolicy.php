<?php

namespace App\Policies;

use App\Enums\HotelWorkerAccess;
use App\Models\Hotel;
use App\Models\Photo;
use App\Models\User;
use App\Services\HotelAccessService;

class PhotoPolicy
{
    public function viewAny(User $user, Hotel $hotel): bool
    {
        return app(HotelAccessService::class)->userCanAccess($user, $hotel, HotelWorkerAccess::Photos);
    }

    public function view(User $user, Photo $photo, Hotel $hotel): bool
    {
        return $this->viewAny($user, $hotel);
    }

    public function create(User $user, Hotel $hotel): bool
    {
        return $this->viewAny($user, $hotel);
    }

    public function update(User $user, Photo $photo, Hotel $hotel): bool
    {
        return $this->viewAny($user, $hotel);
    }

    public function delete(User $user, Photo $photo, Hotel $hotel): bool
    {
        return $this->viewAny($user, $hotel);
    }
}
