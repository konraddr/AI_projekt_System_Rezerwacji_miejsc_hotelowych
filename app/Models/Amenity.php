<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon'];

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'hotel_amenity')
            ->using(HotelAmenity::class)
            ->withPivot('id', 'price')
            ->withTimestamps();
    }

    public function hotelAmenities(): HasMany
    {
        return $this->hasMany(HotelAmenity::class);
    }
}
