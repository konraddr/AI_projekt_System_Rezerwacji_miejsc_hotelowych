<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class HotelAmenity extends Pivot
{
    protected $table = 'hotel_amenity';

    public $incrementing = true;

    protected $fillable = [
        'hotel_id',
        'amenity_id',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function amenity(): BelongsTo
    {
        return $this->belongsTo(Amenity::class);
    }

    public function roomAmenities(): HasMany
    {
        return $this->hasMany(RoomAmenity::class, 'hotel_amenity_id');
    }
}
