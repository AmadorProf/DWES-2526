<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genres = ['Acción', 'Drama', 'Comedia', 'Terror', 'Ciencia Ficción', 'Aventura', 'Romance', 'Thriller'];
        $ratings = ['ATP', '+13', '+16', '+18'];

        return [
            'title' => fake()->sentence(3),
            'director' => fake()->name(),
            'year' => fake()->numberBetween(1980, 2024),
            'genre' => fake()->randomElement($genres),
            'duration' => fake()->numberBetween(80, 180),
            'synopsis' => fake()->paragraph(3),
            'cast' => fake()->name() . ', ' . fake()->name() . ', ' . fake()->name(),
            'country' => fake()->country(),
            'poster' => 'posters/default.jpg',
            'age_rating' => fake()->randomElement($ratings),
            'average_rating' => 0,
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
