<?php

namespace App\Policies;

use App\Models\Photo;
use App\Models\User;

class PhotoPolicy
{
    /**
     * Tymczasowo: każdy zalogowany użytkownik w panelu manage.
     * Po dodaniu hotels.user_id lub workers (Konrad/Daniel) — sprawdzać właściciela obiektu.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Photo $photo): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Photo $photo): bool
    {
        return true;
    }

    public function delete(User $user, Photo $photo): bool
    {
        return true;
    }
}
