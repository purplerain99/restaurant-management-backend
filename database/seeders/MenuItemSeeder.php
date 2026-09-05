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

        $items = [
            // Myanmar Food / Main Dishes ($myanmarFood->id)
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Chicken Fried Rice',
                'description' => 'ကြက်သားထမင်းကြော်',
                'price' => 5000,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Pork Fried Rice',
                'description' => 'ဝက်သားထမင်းကြော်',
                'price' => 5000,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Seafood Fried Rice',
                'description' => 'ပင်လယ်စာထမင်းကြော်',
                'price' => 6500,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Mohinga',
                'description' => 'မုန့်ဟင်းခါး',
                'price' => 3000,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Coconut Chicken Noodles',
                'description' => 'အုန်းနို့ခေါက်ဆွဲ',
                'price' => 3500,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Shan Noodles',
                'description' => 'ရှမ်းခေါက်ဆွဲ',
                'price' => 3500,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Chicken Kyay Oh',
                'description' => 'ကြက်သားကြေးအိုး',
                'price' => 5500,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Pork Kyay Oh',
                'description' => 'ဝက်သားကြေးအိုး',
                'price' => 6000,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Fried Noodles with Chicken',
                'description' => 'ကြက်သားခေါက်ဆွဲကြော်',
                'price' => 5000,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Crispy Pork Belly',
                'description' => 'ဝက်သုံးထပ်သားကြွပ်ကြော်',
                'price' => 8000,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Tea Leaf Salad',
                'description' => 'လက်ဖက်သုပ်',
                'price' => 3000,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Samosa Salad',
                'description' => 'စမူဆာသုပ်',
                'price' => 2500,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Steamed Chicken Rice',
                'description' => 'ကြက်ပေါင်းထမင်း',
                'price' => 6000,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Tom Yum Soup',
                'description' => 'တုံယမ်းဟင်းချို',
                'price' => 7000,
            ],
            [
                'category_id' => $myanmarFood->id,
                'name' => 'Fried Chicken Wings',
                'description' => 'ကြက်တောင်ပံကြော်',
                'price' => 5500,
            ],
        
            // Beverages ($drinks->id)
            [
                'category_id' => $drinks->id,
                'name' => 'Coca Cola',
                'description' => 'အေးမြသော Coca Cola',
                'price' => 2000,
            ],
            [
                'category_id' => $drinks->id,
                'name' => 'Sprite',
                'description' => 'အေးမြသော Sprite',
                'price' => 2000,
            ],
            [
                'category_id' => $drinks->id,
                'name' => 'Iced Green Tea',
                'description' => 'ရေခဲအေး အစိမ်းရောင်လက်ဖက်ရည်',
                'price' => 2500,
            ],
            [
                'category_id' => $drinks->id,
                'name' => 'Myanmar Sweet Tea',
                'description' => 'မြန်မာ့လက်ဖက်ရည်ပူ',
                'price' => 1500,
            ],
            [
                'category_id' => $drinks->id,
                'name' => 'Fresh Lemon Tea',
                'description' => 'သံပုရာလက်ဖက်ရည်',
                'price' => 2500,
            ],
            [
                'category_id' => $drinks->id,
                'name' => 'Avocado Smoothie',
                'description' => 'ထောပတ်သီးဖျော်ရည်',
                'price' => 4000,
            ],
            [
                'category_id' => $drinks->id,
                'name' => 'Fresh Orange Juice',
                'description' => 'လတ်ဆတ်သော လိမ္မော်သီးဖျော်ရည်',
                'price' => 3500,
            ],
            [
                'category_id' => $drinks->id,
                'name' => 'Iced Americano',
                'description' => 'ရေခဲ အမေရိကာနို ကော်ဖီ',
                'price' => 3500,
            ],
            [
                'category_id' => $drinks->id,
                'name' => 'Iced Latte',
                'description' => 'ရေခဲ လတ်တေး ကော်ဖီ',
                'price' => 4000,
            ],
            [
                'category_id' => $drinks->id,
                'name' => 'Bottled Mineral Water',
                'description' => 'သန့်ရှင်းသော သောက်ရေသန့်',
                'price' => 1000,
            ],
        ];
        
        foreach ($items as $item) {
            MenuItem::create([
                'category_id'  => $item['category_id'],
                'name'         => $item['name'],
                'slug'         => Str::slug($item['name']),
                'description'  => $item['description'],
                'price'        => $item['price'],
                'is_available' => true,
            ]);
        }
    }
}