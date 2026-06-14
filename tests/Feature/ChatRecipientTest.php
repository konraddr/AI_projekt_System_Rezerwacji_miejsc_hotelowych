<?php

namespace Tests\Feature;

use App\Enums\HotelWorkerAccess;
use App\Enums\UserPermission;
use App\Models\Hotel;
use App\Models\Message;
use App\Models\User;
use App\Services\ChatRecipientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatRecipientTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_who_messaged_hotel_appears_in_recipient_select_for_owner(): void
    {
        $owner = User::factory()->create(['permission' => UserPermission::Owner]);
        $client = User::factory()->create(['permission' => UserPermission::Client]);

        $hotel = Hotel::query()->create([
            'owner_id' => $owner->id,
            'name' => 'Test Hotel',
            'city' => 'Kraków',
            'address' => 'ul. Testowa 1',
            'description' => 'Opis testowy hotelu.',
        ]);

        $hotel->workers()->attach($owner->id, [
            'permissions' => HotelWorkerAccess::values(),
        ]);

        Message::query()->create([
            'sender_id' => $client->id,
            'receiver_id' => $owner->id,
            'hotel_id' => $hotel->id,
            'content' => 'Czy jest parking?',
        ]);

        $receivers = app(ChatRecipientService::class)->receiversForHotel($hotel, $owner);

        $this->assertTrue($receivers->contains('id', $client->id));
        $this->assertSame(
            $client->id,
            app(ChatRecipientService::class)->defaultReceiverId($hotel, $receivers, $owner)
        );
    }
}
