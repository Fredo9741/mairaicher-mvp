<?php

namespace Database\Seeders;

use App\Models\Bundle;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BundleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Panier du Marché
        $panierMarche = Bundle::updateOrCreate(
            ['name' => 'Panier du Marché'],
            [
                'description' => 'Un assortiment de légumes frais pour toute la semaine',
                'price_cents' => 1200,
                'is_active' => true,
            ]
        );

        $panierMarche->products()->sync([
            Product::where('name', 'Tomates')->first()->id => ['quantity_included' => 1.0],
            Product::where('name', 'Carottes')->first()->id => ['quantity_included' => 1.0],
            Product::where('name', 'Salades')->first()->id => ['quantity_included' => 2.0],
            Product::where('name', 'Courgettes')->first()->id => ['quantity_included' => 1.5],
        ]);

        // Panier Famille
        $panierFamille = Bundle::updateOrCreate(
            ['name' => 'Panier Famille'],
            [
                'description' => 'Un panier généreux pour toute la famille avec légumes variés',
                'price_cents' => 2500,
                'is_active' => true,
            ]
        );

        $panierFamille->products()->sync([
            Product::where('name', 'Tomates')->first()->id => ['quantity_included' => 2.0],
            Product::where('name', 'Carottes')->first()->id => ['quantity_included' => 2.0],
            Product::where('name', 'Pommes de terre')->first()->id => ['quantity_included' => 3.0],
            Product::where('name', 'Salades')->first()->id => ['quantity_included' => 3.0],
            Product::where('name', 'Courgettes')->first()->id => ['quantity_included' => 2.0],
            Product::where('name', 'Haricots verts')->first()->id => ['quantity_included' => 1.0],
        ]);

        // Panier Volaille Complète
        $panierVolaille = Bundle::updateOrCreate(
            ['name' => 'Panier Volaille Complète'],
            [
                'description' => 'Poulet fermier avec des légumes frais pour un repas complet',
                'price_cents' => 2800,
                'is_active' => true,
            ]
        );

        $panierVolaille->products()->sync([
            Product::where('name', 'Poulet fermier')->first()->id => ['quantity_included' => 1.0],
            Product::where('name', 'Pommes de terre')->first()->id => ['quantity_included' => 2.0],
            Product::where('name', 'Carottes')->first()->id => ['quantity_included' => 1.0],
            Product::where('name', 'Haricots verts')->first()->id => ['quantity_included' => 0.5],
        ]);

        // Panier Découverte
        $panierDecouverte = Bundle::updateOrCreate(
            ['name' => 'Panier Découverte'],
            [
                'description' => 'Découvrez nos meilleurs produits à petit prix',
                'price_cents' => 1500,
                'is_active' => true,
            ]
        );

        $panierDecouverte->products()->sync([
            Product::where('name', 'Tomates')->first()->id => ['quantity_included' => 1.0],
            Product::where('name', 'Concombres')->first()->id => ['quantity_included' => 2.0],
            Product::where('name', 'Poivrons')->first()->id => ['quantity_included' => 0.5],
            Product::where('name', 'Salades')->first()->id => ['quantity_included' => 1.0],
            Product::where('name', 'Œufs frais')->first()->id => ['quantity_included' => 1.0],
        ]);
    }
}
