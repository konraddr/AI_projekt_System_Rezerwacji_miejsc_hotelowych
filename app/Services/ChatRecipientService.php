<?php

namespace App\Services;

use App\Enums\HotelWorkerAccess;
use App\Models\Hotel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ChatRecipientService
{
    public function __construct(
        private readonly HotelAccessService $hotelAccess
    ) {}

    /**
     * @return Collection<int, User>
     */
    public function receiversForHotel(Hotel $hotel, User $currentUser): Collection
    {
        $hotel->load('workers');

        $receivers = new Collection;

        foreach ($hotel->workers as $worker) {
            if ($this->hotelAccess->userCanAccess($worker, $hotel, HotelWorkerAccess::Chat)) {
                $receivers->push($worker);
            }
        }

        $participantIds = Message::query()
            ->where('hotel_id', $hotel->id)
            ->select(['sender_id', 'receiver_id'])
            ->get()
            ->flatMap(fn (Message $message) => [$message->sender_id, $message->receiver_id])
            ->unique()
            ->values();

        if ($participantIds->isNotEmpty()) {
            $participants = User::query()
                ->whereIn('id', $participantIds)
                ->get();

            $receivers = $receivers->merge($participants);
        }

        return $receivers
            ->push($currentUser)
            ->unique('id')
            ->filter(fn (User $user) => ! $user->isBanned())
            ->sortBy('name')
            ->values();
    }

    public function defaultReceiverId(Hotel $hotel, Collection $receivers, User $currentUser): int
    {
        $lastInbound = Message::query()
            ->where('hotel_id', $hotel->id)
            ->where('receiver_id', $currentUser->id)
            ->latest('id')
            ->first();

        if (
            $lastInbound !== null
            && $receivers->contains('id', $lastInbound->sender_id)
        ) {
            return $lastInbound->sender_id;
        }

        $other = $receivers->first(fn (User $user) => $user->id !== $currentUser->id);

        return $other?->id ?? $currentUser->id;
    }
}
