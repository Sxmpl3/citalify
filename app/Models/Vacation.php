<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacation extends Model
{
    protected $fillable = ['user_id', 'date', 'start_time', 'end_time'];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isFullDay(): bool
    {
        return $this->start_time === null && $this->end_time === null;
    }
}
