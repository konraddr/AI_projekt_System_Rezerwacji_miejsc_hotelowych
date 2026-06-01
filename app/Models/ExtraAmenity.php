<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraAmenity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'hotel_amenity_id',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function hotelAmenity(): BelongsTo
    {
        return $this->belongsTo(HotelAmenity::class);
    }
}
