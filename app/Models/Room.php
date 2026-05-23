<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'description',
        'capacity',
        'price_per_night',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'price_per_night' => 'decimal:2',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomAmenities(): HasMany
    {
        return $this->hasMany(RoomAmenity::class);
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable')->orderBy('order');
    }
}
