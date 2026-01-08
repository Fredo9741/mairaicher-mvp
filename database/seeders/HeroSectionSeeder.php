<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HeroSection;

class HeroSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HeroSection::create([
            'title' => 'Domaine des Papangues',
            'subtitle' => 'Domaine des Papangues : agriculture, élevage biologique et traditionnel',
            'badge_text' => 'Production locale & Agriculture durable',
            'image' => null, // L'admin pourra uploader l'image via Filament
            'is_active' => true,
        ]);
    }
}
