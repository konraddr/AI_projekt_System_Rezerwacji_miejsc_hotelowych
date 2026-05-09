<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon'];

    // Relacja: Udogodnienie (np. WiFi) może należeć do wielu Hoteli
    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'hotel_amenity')
            ->withPivot('id', 'price')
            ->withTimestamps();
    }
}
