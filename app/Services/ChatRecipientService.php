<?php

namespace App\Services;

use App\Enums\UserPermission;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ChatRecipientService
{
    /**
     * @return Collection<int, User>
     */
    public function receiversForHotel(Hotel $hotel, User $currentUser): Collection
    {
        $receiverIds = Message::query()
            ->where('hotel_id', $hotel->id)
            ->forParticipant($currentUser->id)
            ->get(['sender_id', 'receiver_id'])
            ->flatMap(fn (Message $message) => [$message->sender_id, $message->receiver_id]);

        $receiverIds = $receiverIds
            ->merge($hotel->workers()->pluck('users.id'))
            ->merge(
                Booking::query()
                    ->whereHas('room', fn ($query) => $query->where('hotel_id', $hotel->id))
                    ->pluck('user_id')
            )
            ->merge(User::query()->whereIn('email', config('maciej.admin_emails', []))->pluck('id'))
            ->merge(
                User::query()
                    ->where('permission', '!=', UserPermission::Banned)
                    ->pluck('id')
            )
            ->push($currentUser->id)
            ->unique()
            ->filter()
            ->values();

        return User::query()
            ->whereIn('id', $receiverIds)
            ->orderBy('name')
            ->get();
    }

    public function defaultReceiverId(Collection $receivers, User $currentUser): int
    {
        $other = $receivers->first(fn (User $user) => $user->id !== $currentUser->id);

        return $other?->id ?? $currentUser->id;
    }
}
