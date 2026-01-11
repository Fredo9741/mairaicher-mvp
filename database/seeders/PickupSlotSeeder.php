<?php

namespace Database\Seeders;

use App\Models\PickupSlot;
use Illuminate\Database\Seeder;

class PickupSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supprime les anciens points de retrait (pour éviter les doublons lors du développement)
        PickupSlot::query()->delete();

        $pickupPoints = [
            [
                'name' => 'Parking Covoiturage Saint-Leu Centre',
                'address' => 'Avenue du Général de Gaulle, 97436 Saint-Leu',
                'lat' => -21.1705,
                'lng' => 55.2886,
                'working_hours' => [
                    ['day' => 'tuesday', 'open' => '08:00:00', 'close' => '12:00:00', 'closed' => false],
                    ['day' => 'thursday', 'open' => '08:00:00', 'close' => '12:00:00', 'closed' => false],
                    ['day' => 'saturday', 'open' => '06:00:00', 'close' => '11:00:00', 'closed' => false],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Parking Plage de Saint-Leu',
                'address' => 'Boulevard du Front de Mer, 97436 Saint-Leu',
                'lat' => -21.1751,
                'lng' => 55.2845,
                'working_hours' => [
                    ['day' => 'wednesday', 'open' => '09:00:00', 'close' => '13:00:00', 'closed' => false],
                    ['day' => 'saturday', 'open' => '07:00:00', 'close' => '12:00:00', 'closed' => false],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Parking de Stella Matutina',
                'address' => 'Piton Saint-Leu, 97424 Saint-Leu',
                'lat' => -21.1823,
                'lng' => 55.2922,
                'working_hours' => [
                    ['day' => 'monday', 'open' => '14:00:00', 'close' => '18:00:00', 'closed' => false],
                    ['day' => 'friday', 'open' => '14:00:00', 'close' => '18:00:00', 'closed' => false],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Parking Les Makes',
                'address' => 'Route Forestière des Makes, 97421 Saint-Louis',
                'lat' => -21.1950,
                'lng' => 55.4025,
                'working_hours' => [
                    ['day' => 'saturday', 'open' => '08:00:00', 'close' => '10:00:00', 'closed' => false],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($pickupPoints as $point) {
            PickupSlot::create($point);
        }

        $this->command->info('✅ ' . count($pickupPoints) . ' points de retrait créés avec succès !');
    }
}
