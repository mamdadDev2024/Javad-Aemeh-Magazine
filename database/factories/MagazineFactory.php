<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Magazine>
 */
class MagazineFactory extends Factory
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
            'body' => fake()->text(100),
            'slug' => Str::slug($title),
            'pdf' => fake()->url(),
            'image' => fake()->imageUrl(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
