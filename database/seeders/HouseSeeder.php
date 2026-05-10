<?php

namespace Database\Seeders;

use App\Models\House;
use Illuminate\Database\Seeder;

class HouseSeeder extends Seeder
{
    public function run(): void
    {
        // 15 rumah permanen (blok A)
        foreach (range(1, 15) as $i) {
            House::firstOrCreate(
                ['house_number' => 'A-' . str_pad($i, 2, '0', STR_PAD_LEFT)],
                [
                    'block'          => 'A',
                    'address'        => 'Jl. Mawar No. ' . $i,
                    'ownership_type' => 'permanent',
                    'status'         => 'vacant',
                ]
            );
        }

        // 5 rumah kontrakan (blok B)
        foreach (range(1, 5) as $i) {
            House::firstOrCreate(
                ['house_number' => 'B-' . str_pad($i, 2, '0', STR_PAD_LEFT)],
                [
                    'block'          => 'B',
                    'address'        => 'Jl. Melati No. ' . $i,
                    'ownership_type' => 'rental',
                    'status'         => 'vacant',
                ]
            );
        }
    }
}
