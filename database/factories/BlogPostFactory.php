<?php

namespace Database\Factories;

use App\Enums\BlogPostStatus;
use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(5, true),
            'published_at' => now(),
            'user_id' => 1,
            'blog_category_id' => BlogCategory::inRandomOrder()->first()->id,
            'image' => fake()->imageUrl(),
            'is_status' => fake()->randomElement([BlogPostStatus::DRAFT->value, BlogPostStatus::PUBLISHED->value]),
        ];
    }
}
