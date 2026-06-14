<?php

namespace App\Services;

use App\Enums\HotelWorkerAccess;
use App\Models\Hotel;
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

        return $receivers
            ->push($currentUser)
            ->unique('id')
            ->filter(fn (User $user) => ! $user->isBanned())
            ->sortBy('name')
            ->values();
    }

    public function defaultReceiverId(Collection $receivers, User $currentUser): int
    {
        $other = $receivers->first(fn (User $user) => $user->id !== $currentUser->id);

        return $other?->id ?? $currentUser->id;
    }
}
