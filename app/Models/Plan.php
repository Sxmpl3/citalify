<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'price_monthly', 'max_employees', 'max_bookings_monthly',
        'whatsapp_reminders', 'statistics', 'advanced_statistics', 'online_payment', 'embeddable_widget',
    ];

    protected function casts(): array
    {
        return [
            'whatsapp_reminders' => 'boolean',
            'statistics' => 'boolean',
            'advanced_statistics' => 'boolean',
            'online_payment' => 'boolean',
            'embeddable_widget' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
