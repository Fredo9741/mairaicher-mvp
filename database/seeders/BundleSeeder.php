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
                'composition_indicative' => '1kg de tomates, 1kg de carottes, 2 salades, 1.5kg de courgettes',
                'price_cents' => 1200,
                'quantity' => 20,
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
                'composition_indicative' => '2kg de tomates, 2kg de carottes, 3kg de pommes de terre, 3 salades, 2kg de courgettes, 1kg de haricots verts',
                'price_cents' => 2500,
                'quantity' => 15,
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
                'composition_indicative' => '1 poulet fermier, 2kg de pommes de terre, 1kg de carottes, 500g de haricots verts',
                'price_cents' => 2800,
                'quantity' => 10,
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
                'composition_indicative' => '1kg de tomates, 2 concombres, 500g de poivrons, 1 salade, 1 boîte d\'œufs frais',
                'price_cents' => 1500,
                'quantity' => 25,
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
