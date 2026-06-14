<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_api_token(): void
    {
        $user = User::factory()->create([
            'email' => 'api-client@example.com',
            'password' => 'password',
            'permission' => UserPermission::Client,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'api-client@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'token_type',
                'user' => ['id', 'email', 'permission', 'permission_label'],
            ])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.id', $user->id);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'api-client@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'api-client@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['email']);
    }

    public function test_login_rejects_banned_user(): void
    {
        User::factory()->create([
            'email' => 'banned@example.com',
            'password' => 'password',
            'permission' => UserPermission::Banned,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'banned@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertForbidden()
            ->assertJsonPath('message', 'Twoje konto zostało zablokowane.');
    }

    public function test_notifications_require_authentication(): void
    {
        $this->getJson('/api/notifications')->assertUnauthorized();
    }

    public function test_user_can_list_notifications(): void
    {
        $user = User::factory()->create();
        $this->createNotification($user, 'Pierwsze', false);
        $this->createNotification($user, 'Drugie', true);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/notifications');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'title',
                    'message',
                    'is_read',
                    'created_at',
                ]],
                'links',
                'meta',
            ]);
    }

    public function test_user_can_read_unread_count(): void
    {
        $user = User::factory()->create();
        $this->createNotification($user, 'Unread', false);
        $this->createNotification($user, 'Read', true);

        Sanctum::actingAs($user);

        $this->getJson('/api/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('count', 1);
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notification = $this->createNotification($user, 'Do odczytu', false);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/notifications/'.$notification->id);

        $response
            ->assertOk()
            ->assertJsonPath('data.is_read', true)
            ->assertJsonPath('data.title', 'Do odczytu');

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_mark_foreign_notification(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $notification = $this->createNotification($owner, 'Prywatne', false);

        Sanctum::actingAs($intruder);

        $this->patchJson('/api/notifications/'.$notification->id)->assertNotFound();
    }

    public function test_web_session_can_poll_unread_count_via_api(): void
    {
        $user = User::factory()->create();
        $this->createNotification($user, 'Badge', false);

        $this->actingAs($user)
            ->getJson('/api/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('count', 1);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $this->createNotification($user, 'A', false);
        $this->createNotification($user, 'B', false);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/notifications/read-all');

        $response
            ->assertOk()
            ->assertJsonPath('marked_count', 2);

        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
    }

    private function createNotification(User $user, string $title, bool $read): DatabaseNotification
    {
        return DatabaseNotification::query()->create([
            'id' => (string) Str::uuid(),
            'type' => 'Tests\\TestNotification',
            'notifiable_type' => $user->getMorphClass(),
            'notifiable_id' => $user->getKey(),
            'data' => [
                'title' => $title,
                'message' => 'Treść powiadomienia testowego.',
                'url' => '/notifications',
            ],
            'read_at' => $read ? now() : null,
        ]);
    }
}
