<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
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

        // Supprimer l'ancienne image lors de l'update
        static::updating(function ($bundle) {
            if ($bundle->isDirty('image') && $bundle->getOriginal('image')) {
                Storage::disk('r2')->delete($bundle->getOriginal('image'));
            }
        });

        // Supprimer l'image lors de la suppression du panier
        static::deleting(function ($bundle) {
            if ($bundle->image) {
                Storage::disk('r2')->delete($bundle->image);
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

    public function isAvailable(int $bundleQuantity = 1): bool
    {
        if (!$this->is_active) {
            return false;
        }

        foreach ($this->products as $product) {
            $quantityNeeded = $product->pivot->quantity_included * $bundleQuantity;
            if (!$product->isAvailable($quantityNeeded)) {
                return false;
            }
        }

        return true;
    }

    public function getStockErrorMessage(int $bundleQuantity = 1): string
    {
        if (!$this->is_active) {
            return 'Ce panier n\'est plus disponible.';
        }

        // Trouver le produit le plus limitant
        $mostLimitingProduct = null;
        $minAvailableBundles = PHP_INT_MAX;

        foreach ($this->products as $product) {
            $quantityNeeded = $product->pivot->quantity_included * $bundleQuantity;
            $availableStock = $product->stock;
            $maxBundles = floor($availableStock / $product->pivot->quantity_included);

            if ($maxBundles < $minAvailableBundles) {
                $minAvailableBundles = $maxBundles;
                $mostLimitingProduct = $product;
            }
        }

        // Si on a trouvé un produit limitant
        if ($mostLimitingProduct && $minAvailableBundles < $bundleQuantity) {
            if ($minAvailableBundles == 0) {
                return "Stock insuffisant pour le panier '{$this->name}'. Le produit '{$mostLimitingProduct->name}' n'est plus disponible en quantité suffisante.";
            }

            return "Stock insuffisant pour {$bundleQuantity} panier(s) '{$this->name}'. Maximum disponible : {$minAvailableBundles} panier(s) (limité par le stock de '{$mostLimitingProduct->name}').";
        }

        return '';
    }
}
