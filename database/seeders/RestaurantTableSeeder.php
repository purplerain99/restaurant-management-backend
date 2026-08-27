<?php

namespace Database\Seeders;

use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RestaurantTableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            RestaurantTable::create([
                'name' => "Table {$i}",

                'table_code' =>
                    'TBL_' .
                    Str::random(16),

                'capacity' => 4,

                'status' => 'available',
            ]);
        }
    }
}