<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroSection extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'badge_text',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // Supprimer l'ancienne image lors de l'update
        static::updating(function ($hero) {
            if ($hero->isDirty('image') && $hero->getOriginal('image')) {
                Storage::disk('r2')->delete($hero->getOriginal('image'));
            }
        });

        // Supprimer l'image lors de la suppression
        static::deleting(function ($hero) {
            if ($hero->image) {
                Storage::disk('r2')->delete($hero->image);
            }
        });
    }

    /**
     * Récupère la section hero active
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }
}
