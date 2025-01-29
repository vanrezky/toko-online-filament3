<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributePatternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat atribut "Pattern" (Pola)
        $attribute = ProductAttribute::create(['name' => 'Pola', 'is_global' => true, 'status' => 'approved']);

        // Daftar pola umum
        $patterns = [
            'Polos',
            'Garis-Garis',
            'Kotak-Kotak',
            'Polkadot',
            'Bunga',
            'Abstrak',
            'Geometris',
            'Kamboja',
            'Paisley',
            'Camo (Kamuflase)',
            'Chevron',
            'Tie-Dye',
            'Zigzag',
            'Animal Print',
            'Tribal',
            'Herringbone',
            'Houndstooth',
            'Ombre',
            'Stripes (Garis Horizontal)',
            'Motif Etnik',
            'Motif Batik',
            'Motif Songket',
            'Motif Tenun',
            'Motif Bordir'
        ];

        // Tambahkan opsi pola ke atribut
        foreach ($patterns as $pattern) {
            $attribute->options()->create(['name' => $pattern, 'is_global' => true, 'status' => 'approved']);
        }
    }
}
