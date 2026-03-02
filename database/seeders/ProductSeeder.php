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
        $user = User::first() ?? User::factory()->create();
        $categories = Category::active()->get();
        if ($categories->isEmpty()) {
            $categories = Category::factory()->count(3)->create(['is_active' => true]);
        }
        $warehouse = Warehouse::active()->first();

        Product::factory()->count(20)->create([
            'user_id' => $user->id,
            'category_id' => fn() => $categories->random()->id,
            'warehouse_id' => $warehouse?->id,
        ]);
    }
}
