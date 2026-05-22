<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function amenities(){
        return $this->belongsToMany(Amenity::class, 'hotel_amenity')
            ->withPivot('id','price')
            ->withTimestamps();
    }
}
