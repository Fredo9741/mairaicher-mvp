<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_cents',
        'unit',
        'stock',
        'image',
        'category',
        'is_active',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'stock' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class, 'bundle_product')
            ->withPivot('quantity_included')
            ->withTimestamps();
    }

    public function getPriceAttribute(): float
    {
        return $this->price_cents / 100;
    }

    public function setPriceAttribute($value): void
    {
        $this->price_cents = $value * 100;
    }

    public function isAvailable(float $quantity = 1): bool
    {
        return $this->is_active && $this->stock >= $quantity;
    }

    public function getStockErrorMessage(): string
    {
        if ($this->unit === 'kg') {
            $stockText = $this->stock == 1 ? '1 kilo' : $this->stock . ' kilos';
            return "Il ne reste que {$stockText} en stock.";
        } else {
            $stockText = $this->stock == 1 ? '1 pièce' : $this->stock . ' pièces';
            return "Il ne reste que {$stockText} en stock.";
        }
    }

    public function decrementStock(float $quantity): void
    {
        $this->decrement('stock', $quantity);
    }
}
