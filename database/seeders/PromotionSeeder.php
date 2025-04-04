<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promotions = collect([
            [
                'position' => 'top',
                'title' => 'Promotion 1',
                'description' => 'Promotion 1 description',
                'is_active' => true,
                'start_at' => now(),
                'end_at' => now()->addDays(7),
                'target_link' => '#',
                'target_anchor' => '_self',
            ],
            [
                'position' => 'top',
                'title' => 'Promotion 2',
                'description' => 'Promotion 2 description',
                'is_active' => true,
                'start_at' => now(),
                'end_at' => now()->addDays(7),
                'target_link' => '#',
                'target_anchor' => '_self',
            ]
        ]);

        $promotions->each(fn($promotion) => \App\Models\Promotion::create($promotion));
    }
}
