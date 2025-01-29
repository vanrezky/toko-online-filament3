<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attribute = ProductAttribute::create(['name' => 'Warna', 'is_global' => true, 'status' => 'approved']);

        $colors = [
            'Merah',
            'Orange',
            'Kuning',
            'Hijau',
            'Biru',
            'Ungu',
            'Merah Muda',
            'Biru Muda',
            'Putih',
            'Abu Abu',
            'Perak',
            'Cokelat',
            'Khaki',
            'Hitam',
            'Emas',
            'Beige',
            'Bronze',
            'Copper',
            'Krem',
            'Biru Tua',
            'Cokelat Tua',
            'Hijau Tua',
            'Abu Tua',
            'Orange Tua',
            'Pink Tua',
            'Ungu Tua',
            'Merah Tua',
            'Kuning Tua',
            'Dusty Pink',
            'Fuschia',
            'Lavender',
            'Hijau Muda',
            'Abu Abu Muda',
            'Orange Muda',
            'Pink Muda',
            'Kuning Muda',
            'Lilac',
            'Lime',
            'Mint',
            'Off-white',
            'Olive',
            'Pink Peach',
            'Magenta Ungu',
            'Rose Gold',
            'Sage',
            'Tan',
            'Teal',
            'Violet',
            'Mustard'
        ];

        foreach ($colors as $key => $color) {
            $attribute->options()->create(['name' => $color, 'is_global' => true, 'status' => 'approved']);
        }
    }
}
