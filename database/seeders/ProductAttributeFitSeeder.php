<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeFitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attribute = ProductAttribute::create(['name' => 'Kesesuaian', 'is_global' => true, 'status' => 'approved']);

        $fits = [
            'Slim Fit',
            'Regular Fit',
            'Loose Fit',
            'Relaxed Fit',
            'Oversized Fit',
            'Skinny Fit',
            'Tailored Fit',
            'Comfort Fit',
            'Baggy Fit'
        ];

        foreach ($fits as $fit) {
            $attribute->options()->create(['name' => $fit, 'is_global' => true, 'status' => 'approved']);
        }
    }
}
