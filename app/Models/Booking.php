<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'check_in',
        'check_out',
        'total_price',
        'payment_status',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'total_price' => 'decimal:2',
            'payment_status' => PaymentStatus::class,
            'status' => BookingStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function extraAmenities(): HasMany
    {
        return $this->hasMany(ExtraAmenity::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function qualifiesForReview(): bool
    {
        return $this->payment_status === PaymentStatus::Paid
            && $this->status === BookingStatus::Completed;
    }

    public function canPay(): bool
    {
        return $this->status === BookingStatus::Active
            && $this->payment_status === PaymentStatus::Pending;
    }

    public function canCancel(): bool
    {
        return $this->status === BookingStatus::Active
            && $this->check_in->gte(now()->startOfDay());
    }
}
