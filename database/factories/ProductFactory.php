<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        $price = $this->faker->randomFloat(2, 50000, 2000000);
        $salePrice = $this->faker->boolean(30) ? $price * 0.8 : null;

        return [
            'uuid' => $this->faker->uuid(),
            'name' => $name,
            'slug' => Str::slug($name),
            'category_id' => Category::factory(),
            'warehouse_id' => null, // Assuming warehouses might not exist yet, or keep it null
            'digital' => false,
            'description' => $this->faker->paragraph(5),
            'code' => strtoupper($this->faker->bothify('PROD-####-????')),
            'stock' => $this->faker->numberBetween(10, 100),
            'security_stock' => 5,
            'weight' => $this->faker->numberBetween(100, 2000),
            'price' => $price,
            'sale_price' => $salePrice,
            'min_order' => 1,
            'is_active' => true,
            'user_id' => User::factory(),
        ];
    }
}
