<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'name', 'description', 'city', 'address', 'latitude', 'longitude'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
