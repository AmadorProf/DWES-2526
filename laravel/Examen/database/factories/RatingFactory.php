<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'movie_id' => Movie::inRandomOrder()->first()->id,
            'score' => fake()->numberBetween(1, 5),
        ];
    }
}
