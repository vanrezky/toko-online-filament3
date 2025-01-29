<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attribute = ProductAttribute::create(['name' => 'Penggunaan', 'is_global' => true, 'status' => 'approved']);

        $usages = [
            'Harian',
            'Formal',
            'Olahraga',
            'Pesta',
            'Kerja',
            'Tidur',
            'Liburan',
            'Musim Dingin',
            'Musim Panas',
            'Outdoor',
            'Indoor',
            'Traveling',
            'Santai',
            'Resmi',
            'Kantor'
        ];

        foreach ($usages as $usage) {
            $attribute->options()->create(['name' => $usage, 'is_global' => true, 'status' => 'approved']);
        }
    }
}
