<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserPermission;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

#[Fillable(['name', 'last_name', 'email', 'phone', 'password', 'permission'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasPushSubscriptions, Notifiable;

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function workerHotels(): BelongsToMany
    {
        return $this->belongsToMany(
            Hotel::class,
            'workers',
            'worker_id',
            'hotel_id'
        )
            ->using(HotelWorker::class)
            ->withPivot('permissions');
    }

    public function hasPermission(UserPermission ...$permissions): bool
    {
        return in_array($this->permission, $permissions, true);
    }

    public function isBanned(): bool
    {
        return $this->permission?->isBanned() ?? false;
    }

    public function canAccessHotelPanel(): bool
    {
        if ($this->hasPermission(
            UserPermission::Administrator,
            UserPermission::Owner,
            UserPermission::Worker,
        )) {
            return true;
        }

        return $this->workerHotels()->exists();
    }

    public function canCreateHotel(): bool
    {
        if ($this->hasPermission(
            UserPermission::Administrator,
            UserPermission::Owner,
        )) {
            return true;
        }

        return ! $this->workerHotels()->exists();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permission' => UserPermission::class,
        ];
    }
}