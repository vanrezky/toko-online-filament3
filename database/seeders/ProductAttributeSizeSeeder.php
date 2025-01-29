<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attribute = ProductAttribute::create(['name' => 'Ukuran', 'is_global' => true, 'status' => 'approved']);

        $sizes = [
            'XS',
            'S',
            'M',
            'L',
            'XL',
            'XXL',
            'XXXL',
            '4XL',
            '5XL',
            '6XL',
            '7XL',
            'All size fit to XL',
            'All Size fit to XXL',
        ];

        foreach ($sizes as $key => $size) {
            $attribute->options()->create(['name' => $size, 'is_global' => true, 'status' => 'approved']);
        }
    }
}
