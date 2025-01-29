<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat atribut "Brand" (Merek)
        $attribute = ProductAttribute::create(['name' => 'Merek', 'is_global' => true, 'status' => 'approved']);

        // Daftar merek terkenal di dunia dan Indonesia
        $brands = [
            // Fashion dan pakaian
            'Nike',
            'Adidas',
            'Puma',
            'Under Armour',
            'Uniqlo',
            'H&M',
            'Zara',
            'Louis Vuitton',
            'Gucci',
            'Prada',
            'Hermès',
            'Chanel',
            'Supreme',
            'Levi\'s',

            // Elektronik dan gadget
            'Apple',
            'Samsung',
            'Sony',
            'LG',
            'Xiaomi',
            'Oppo',
            'Vivo',
            'Asus',
            'HP (Hewlett-Packard)',
            'Lenovo',
            'Microsoft',
            'Dell',
            'Acer',

            // Kosmetik dan kecantikan
            'L\'Oréal',
            'Maybelline',
            'MAC',
            'Estee Lauder',
            'Dior',
            'Fenty Beauty',
            'Wardah',
            'Emina',
            'Make Over',

            // Merek lokal Indonesia
            'Eiger',
            'Consina',
            'Tupperware',
            'Indomie',
            'Pocari Sweat',
            'Gojek',
            'Tokopedia',

            // Merek kendaraan
            'Toyota',
            'Honda',
            'Yamaha',
            'Suzuki',
            'BMW',
            'Mercedes-Benz',
            'Tesla',
            'Ford',
            'Hyundai',
            'Kia',

            // Merek global lainnya
            'IKEA',
            'Starbucks',
            'Coca-Cola',
            'Pepsi',
            'Nestlé',
            'KFC',
            'McDonald\'s',
            'Burger King'
        ];

        // Tambahkan opsi merek ke atribut
        foreach ($brands as $brand) {
            $attribute->options()->create(['name' => $brand, 'is_global' => true, 'status' => 'approved']);
        }
    }
}
