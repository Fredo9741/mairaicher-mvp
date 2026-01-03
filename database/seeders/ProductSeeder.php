<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Légumes
            [
                'name' => 'Tomates',
                'description' => 'Tomates fraîches du jardin',
                'price_cents' => 350,
                'unit' => 'kg',
                'stock' => 50.00,
                'category' => 'legume',
                'is_active' => true,
            ],
            [
                'name' => 'Carottes',
                'description' => 'Carottes bio cultivées localement',
                'price_cents' => 280,
                'unit' => 'kg',
                'stock' => 30.00,
                'category' => 'legume',
                'is_active' => true,
            ],
            [
                'name' => 'Salades',
                'description' => 'Salade fraîche du jour',
                'price_cents' => 150,
                'unit' => 'piece',
                'stock' => 25.00,
                'category' => 'legume',
                'is_active' => true,
            ],
            [
                'name' => 'Courgettes',
                'description' => 'Courgettes du jardin',
                'price_cents' => 320,
                'unit' => 'kg',
                'stock' => 40.00,
                'category' => 'legume',
                'is_active' => true,
            ],
            [
                'name' => 'Pommes de terre',
                'description' => 'Pommes de terre variété Agria',
                'price_cents' => 220,
                'unit' => 'kg',
                'stock' => 100.00,
                'category' => 'legume',
                'is_active' => true,
            ],
            [
                'name' => 'Haricots verts',
                'description' => 'Haricots verts extra fins',
                'price_cents' => 450,
                'unit' => 'kg',
                'stock' => 15.00,
                'category' => 'legume',
                'is_active' => true,
            ],
            [
                'name' => 'Concombres',
                'description' => 'Concombres croquants',
                'price_cents' => 200,
                'unit' => 'piece',
                'stock' => 20.00,
                'category' => 'legume',
                'is_active' => true,
            ],
            [
                'name' => 'Poivrons',
                'description' => 'Poivrons rouges et verts',
                'price_cents' => 380,
                'unit' => 'kg',
                'stock' => 18.00,
                'category' => 'legume',
                'is_active' => true,
            ],

            // Volailles
            [
                'name' => 'Poulet fermier',
                'description' => 'Poulet fermier élevé en plein air',
                'price_cents' => 1500,
                'unit' => 'piece',
                'stock' => 10.00,
                'category' => 'volaille',
                'is_active' => true,
            ],
            [
                'name' => 'Pintade',
                'description' => 'Pintade fermière de qualité',
                'price_cents' => 1800,
                'unit' => 'piece',
                'stock' => 5.00,
                'category' => 'volaille',
                'is_active' => true,
            ],
            [
                'name' => 'Canard',
                'description' => 'Canard fermier élevé localement',
                'price_cents' => 2200,
                'unit' => 'piece',
                'stock' => 4.00,
                'category' => 'volaille',
                'is_active' => true,
            ],

            // Autres
            [
                'name' => 'Œufs frais',
                'description' => 'Boîte de 6 œufs frais du jour',
                'price_cents' => 350,
                'unit' => 'piece',
                'stock' => 30.00,
                'category' => 'autre',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
