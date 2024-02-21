<?php

namespace Database\Seeders;

use App\Models\SubDistrict;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::create([
            'name' => 'Warehouse 1',
            'sub_district_id' => SubDistrict::first()->id,
            'address' => 'Jl. Jendral Sudirman No. 1',
            'contact_name' => 'John Doe',
            'contact_phone' => '081234567890',
            'courier' => 'JNE',
            'description' => 'Warehouse 1 description',
            'is_active' => true,
        ]);
    }
}
