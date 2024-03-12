<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CountrySeeder::class,
            ProvinceSeeder::class,
            WarehouseSeeder::class,
            CategorySeeder::class,
            SliderSeeder::class,
            PromotionSeeder::class,
            ProductSeeder::class,
            // VoucherSeeder::class
            CustomerSeeder::class,
            DistributorLevelSeeder::class,
            PageSeeder::class
        ]);
    }
}
