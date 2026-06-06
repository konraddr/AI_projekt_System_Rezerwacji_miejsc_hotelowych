<?php

namespace App\Models;

use App\Enums\ReportStatus;
use App\Enums\ReportTitle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'reason',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'title' => ReportTitle::class,
            'status' => ReportStatus::class,
        ];
    }

    protected $attributes = [
        'title' => 'hotel_nie_odpowiada',
        'status' => 'pending',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
