<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Bundle extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_cents',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bundle) {
            if (empty($bundle->slug)) {
                $bundle->slug = Str::slug($bundle->name);
            }
        });
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'bundle_product')
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

    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        foreach ($this->products as $product) {
            $quantityNeeded = $product->pivot->quantity_included;
            if (!$product->isAvailable($quantityNeeded)) {
                return false;
            }
        }

        return true;
    }
}
