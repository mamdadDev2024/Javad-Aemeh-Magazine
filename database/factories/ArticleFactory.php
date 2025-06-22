<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(2), // Ensure this is a string
            'body' => fake()->text(50000), // Ensure this is a string
            'status' => 1, // Example static value
            'image' => "images/personal-image.png", // Or provide a valid path
            'user_id' => rand(1, 10), // Assuming user_id exists
        ];
    }
}
