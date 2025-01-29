<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat atribut "Material"
        $attribute = ProductAttribute::create(['name' => 'Material', 'is_global' => true, 'status' => 'approved']);

        // Daftar material umum
        $materials = [
            'Katun',
            'Poliester',
            'Nilon',
            'Wol',
            'Denim',
            'Rayon',
            'Linen',
            'Spandeks',
            'Akrilik',
            'Kulit Asli',
            'Kulit Sintetis',
            'Sutra',
            'Kanvas',
            'Tweed',
            'Chiffon',
            'Jersey',
            'Flanel',
            'Kashmir',
            'Taffeta',
            'Organza',
            'Beludru',
            'Lateks',
            'Mesh',
            'Renda',
            'Viscose'
        ];

        // Tambahkan opsi material ke atribut
        foreach ($materials as $material) {
            $attribute->options()->create(['name' => $material, 'is_global' => true, 'status' => 'approved']);
        }
    }
}
