<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeAgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat atribut "Age" (Usia)
        $attribute = ProductAttribute::create(['name' => 'Usia', 'is_global' => true, 'status' => 'approved']);

        // Daftar kategori usia
        $ages = [
            'Bayi (0-12 bulan)',
            'Balita (1-3 tahun)',
            'Anak-Anak (4-12 tahun)',
            'Remaja (13-17 tahun)',
            'Dewasa (18-59 tahun)',
            'Lansia (60+ tahun)'
        ];

        // Tambahkan opsi usia ke atribut
        foreach ($ages as $age) {
            $attribute->options()->create(['name' => $age, 'is_global' => true, 'status' => 'approved']);
        }
    }
}
