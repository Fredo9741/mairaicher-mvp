<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PickupSlot extends Model
{
    protected $fillable = [
        'name',
        'lat',
        'lng',
        'address',
        'working_hours',
        'is_active',
    ];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'working_hours' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Générer le time_range pour l'affichage (basé sur le nom du slot)
     */
    public function getTimeRangeAttribute(): string
    {
        return $this->name ?? 'Créneau non défini';
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
