<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ProductAttributeColorSeeder::class,
            ProductAttributeSizeSeeder::class,
            ProductAttributeMaterialSeeder::class,
            ProductAttributePatternSeeder::class,
            ProductAttributeStyleSeeder::class,
            ProductAttributeBrandSeeder::class,
            ProductAttributeGenderSeeder::class,
            ProductAttributeAgeSeeder::class,
            ProductAttributeSeasonSeeder::class,
            ProductAttributeUsageSeeder::class,
            ProductAttributeFitSeeder::class,
        ]);
    }
}
