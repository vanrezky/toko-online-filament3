<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeStyleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat atribut "Style" (Gaya)
        $attribute = ProductAttribute::create(['name' => 'Gaya', 'is_global' => true, 'status' => 'approved']);

        // Daftar gaya umum
        $styles = [
            'Kasual',
            'Formal',
            'Vintage',
            'Bohemian (Boho)',
            'Minimalis',
            'Sporty',
            'Chic',
            'Streetwear',
            'Elegan',
            'Tradisional',
            'Retro',
            'Preppy',
            'Klasik',
            'Edgy',
            'Grunge',
            'Modern',
            'Glamor',
            'Etnik',
            'Romantis',
            'Smart Casual',
            'Semi-Formal',
            'Militer',
            'Cowboy',
            'Gothic',
            'Urban',
            'Artsy'
        ];

        // Tambahkan opsi gaya ke atribut
        foreach ($styles as $style) {
            $attribute->options()->create(['name' => $style, 'is_global' => true, 'status' => 'approved']);
        }
    }
}
