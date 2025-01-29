<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeSeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat atribut "Season" (Musim)
        $attribute = ProductAttribute::create(['name' => 'Musim', 'is_global' => true, 'status' => 'approved']);

        // Daftar musim umum
        $seasons = [
            'Musim Panas (Summer)',
            'Musim Dingin (Winter)',
            'Musim Semi (Spring)',
            'Musim Gugur (Autumn/Fall)',
            'Musim Hujan',
            'Musim Kemarau',
            'Musim Liburan',
            'Musim Festival'
        ];

        // Tambahkan opsi musim ke atribut
        foreach ($seasons as $season) {
            $attribute->options()->create(['name' => $season, 'is_global' => true, 'status' => 'approved']);
        }
    }
}
