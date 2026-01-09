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
        $slots = [
            [
                'name' => 'Matin 9h-12h',
                'start_time' => '09:00',
                'end_time' => '12:00',
                'is_active' => true,
            ],
            [
                'name' => 'Après-midi 14h-18h',
                'start_time' => '14:00',
                'end_time' => '18:00',
                'is_active' => true,
            ],
            [
                'name' => 'Fin de journée 18h-20h',
                'start_time' => '18:00',
                'end_time' => '20:00',
                'is_active' => true,
            ],
        ];

        foreach ($slots as $slot) {
            PickupSlot::updateOrCreate(
                ['name' => $slot['name']],
                $slot
            );
        }
    }
}
