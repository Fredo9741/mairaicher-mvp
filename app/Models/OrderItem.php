<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_type',
        'item_id',
        'item_name',
        'quantity',
        'unit_price_cents',
        'total_price_cents',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price_cents' => 'integer',
        'total_price_cents' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'item_id')->where('item_type', 'product');
    }

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class, 'item_id')->where('item_type', 'bundle');
    }

    public function getUnitPriceAttribute(): float
    {
        return $this->unit_price_cents / 100;
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->total_price_cents / 100;
    }
}
