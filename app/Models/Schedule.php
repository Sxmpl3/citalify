<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = ['employee_id', 'day_of_week', 'open_time', 'close_time'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
