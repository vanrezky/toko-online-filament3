<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeGenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat atribut "Gender"
        $attribute = ProductAttribute::create(['name' => 'Gender', 'is_global' => true, 'status' => 'approved']);

        // Daftar kategori gender
        $genders = [
            'Pria',
            'Wanita',
            'Unisex',
            'Anak Laki-Laki',
            'Anak Perempuan',
            'Bayi Laki-Laki',
            'Bayi Perempuan',
        ];

        // Tambahkan opsi gender ke atribut
        foreach ($genders as $gender) {
            $attribute->options()->create(['name' => $gender, 'is_global' => true, 'status' => 'approved']);
        }
    }
}
