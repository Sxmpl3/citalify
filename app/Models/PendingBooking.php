<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingBooking extends Model
{
    protected $fillable = [
        'user_id', 'employee_id', 'service_id', 'customer_name',
        'customer_phone', 'customer_email', 'starts_at', 'ends_at',
        'price', 'notes', 'verification_code', 'expires_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'expires_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
