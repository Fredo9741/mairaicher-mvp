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

    /**
     * Get the product if this is a product order item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'item_id')
            ->withDefault([
                'name' => 'Produit supprimé',
                'price_cents' => 0,
            ]);
    }

    /**
     * Get the bundle if this is a bundle order item
     */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class, 'item_id')
            ->withDefault([
                'name' => 'Panier supprimé',
                'price_cents' => 0,
            ]);
    }

    /**
     * Get the actual item (product or bundle) based on item_type
     */
    public function getItemAttribute()
    {
        if ($this->item_type === 'product') {
            return $this->product;
        } elseif ($this->item_type === 'bundle') {
            return $this->bundle;
        }
        return null;
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
