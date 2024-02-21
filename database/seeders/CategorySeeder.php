<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = collect([
            [
                'name' => 'Clothes',
                'slug' => 'clothes',
            ],
            [
                'name' => 'Pants',
                'slug' => 'pants',
            ],
            [
                'name' => 'Shoes',
                'slug' => 'shoes',
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
            ],
            [
                'name' => 'Jewellery',
                'slug' => 'jewellery',
            ],
        ]);

        $data->each(fn ($category) => \App\Models\Category::create($category));
    }
}
