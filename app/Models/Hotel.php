<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'city',
        'address',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function hotelAmenities(): HasMany
    {
        return $this->hasMany(HotelAmenity::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'hotel_amenity')
            ->using(HotelAmenity::class)
            ->withPivot('id', 'price')
            ->withTimestamps();
    }

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'workers',
            'hotel_id',
            'worker_id'
        )
            ->using(HotelWorker::class)
            ->withPivot('permissions');
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable')->orderBy('order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
