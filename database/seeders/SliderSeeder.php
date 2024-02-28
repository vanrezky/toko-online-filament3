<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = collect([
            [
                'description' => 'Slider 1 description',
                'image' => 'https://via.placeholder.com/800x400.png?text=Slider+1',
                'is_active' => true,
                'start_at' => now(),
                'end_at' => now()->addDays(7),
                'target_link' => '#',
                'target_anchor' => '_self',
            ],
            [
                'description' => 'Slider 2 description',
                'image' => 'https://via.placeholder.com/800x400.png?text=Slider+2',
                'is_active' => true,
                'start_at' => now(),
                'end_at' => now()->addDays(7),
                'link' => '#',
                'target' => '_self',
            ]
        ]);

        $sliders->each(fn ($slider) => \App\Models\Slider::create($slider));
    }
}
