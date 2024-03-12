<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = collect([
            [
                'title' => fake()->title(),
                'content' => fake()->paragraph(),
                'slug' => fake()->slug(),
                'image' => fake()->imageUrl(),
                'published_at' => now(),
                'order' => 1
            ]
        ]);

        $pages->each(fn ($page) => Page::create($page));
    }
}
