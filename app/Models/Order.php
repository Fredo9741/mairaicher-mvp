<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'total_price_cents',
        'pickup_date',
        'pickup_slot_id',
        'status',
        'stripe_payment_intent_id',
        'notes',
    ];

    protected $casts = [
        'total_price_cents' => 'integer',
        'pickup_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'CMD-' . strtoupper(uniqid());
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function pickupSlot(): BelongsTo
    {
        return $this->belongsTo(PickupSlot::class)
            ->withDefault([
                'name' => 'Créneau supprimé',
                'is_active' => false,
            ]);
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->total_price_cents / 100;
    }

    public function setTotalPriceAttribute($value): void
    {
        $this->total_price_cents = $value * 100;
    }
}
