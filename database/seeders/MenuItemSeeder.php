<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $myanmarFood = Category::where(
            'slug',
            'myanmar-food'
        )->first();

        $drinks = Category::where(
            'slug',
            'drinks'
        )->first();

        MenuItem::create([
            'category_id' => $myanmarFood->id,
            'name' => 'Chicken Fried Rice',
            'slug' => Str::slug(
                'Chicken Fried Rice'
            ),
            'description' => 'ကြက်သားထမင်းကြော်',
            'price' => 5000,
            'is_available' => true,
        ]);

        MenuItem::create([
            'category_id' => $drinks->id,
            'name' => 'Coca Cola',
            'slug' => Str::slug('Coca Cola'),
            'description' => 'အေးမြသော Coca Cola',
            'price' => 2000,
            'is_available' => true,
        ]);
    }
}