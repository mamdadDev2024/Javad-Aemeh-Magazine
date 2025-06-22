<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "title" => $this->faker->sentence(),
            "body" => $this->faker->paragraph(),
            'image' => "images/personal-image.png", // Or provide a valid path
            "location" => implode(' ', $this->faker->words(4)), // Join words into a string
            "status" => true
        ];
    }
}
