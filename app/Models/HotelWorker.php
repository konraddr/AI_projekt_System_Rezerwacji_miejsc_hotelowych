<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class HotelWorker extends Pivot
{
    protected $table = 'workers';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'permissions' => 'array',
    ];
}
