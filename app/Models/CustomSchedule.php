<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomSchedule extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'open_time',
        'close_time',
        'is_closed',
        'break_start',
        'break_end',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
