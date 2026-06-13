<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\Message;
use App\Models\User;
use App\Services\HotelAccessService;

class MessagePolicy
{
    public function viewAny(User $user, Hotel $hotel): bool
    {
        return app(HotelAccessService::class)->userCanUseHotelChat($user, $hotel);
    }

    public function create(User $user, Hotel $hotel): bool
    {
        return $this->viewAny($user, $hotel);
    }

    public function participate(User $user, Message $message): bool
    {
        return $user->id === $message->sender_id || $user->id === $message->receiver_id;
    }
}
