<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAmenity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'hotel_amenity_id',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function hotelAmenity(): BelongsTo
    {
        return $this->belongsTo(HotelAmenity::class, 'hotel_amenity_id');
    }
}
