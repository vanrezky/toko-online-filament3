<?php

namespace Database\Seeders;

use App\Models\Flashsale;
use App\Models\Product;
use App\Models\ProductFlashsale;
use Illuminate\Database\Seeder;

class FlashsaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::active()->take(10)->get();

        if ($products->isEmpty()) {
            return;
        }

        $flashsale = Flashsale::create([
            'name' => 'Midnight Madness Sale',
            'description' => 'Huge discounts on selected items for a limited time!',
            'start_time' => now()->subHours(1),
            'end_time' => now()->addHours(5),
        ]);

        $products->each(function ($product) use ($flashsale) {
            ProductFlashsale::create([
                'product_id' => $product->id,
                'flashsale_id' => $flashsale->id,
                'discount_percentage' => rand(10, 50),
                'stock' => rand(5, 20),
            ]);
        });
    }
}
