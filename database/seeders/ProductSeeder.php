<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'images' => ['https://placehold.co/600x400?text=product+1'],
            'name' => 'product first on web',
            'slug' => 'product-first-on-web',
            'category_id' => Category::active()->first()->id,
            'warehouse_id' => Warehouse::active()->first()->id,
            'digital' => 0,
            'digital_url' => null,
            'description' => fake()->paragraph(),
            'code' => 'PR848293',
            'stock' => 50,
            'weight' => 1000,
            'price' => 500000,
            'sale_price' => 650000,
            'afiliate_price' => 2500,
            'min_order' => 1,
            'user_id' => User::first()->id
        ]);
    }
}
