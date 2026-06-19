<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating;
use App\Models\User;
use App\Models\Movie;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $movies = Movie::all();

        // Crear valoraciones aleatorias evitando duplicados
        foreach ($movies as $movie) {
            // Cada película recibe entre 1 y 5 valoraciones
            $numRatings = rand(1, 5);
            $usersToRate = $users->random($numRatings);

            foreach ($usersToRate as $user) {
                Rating::create([
                    'user_id' => $user->id,
                    'movie_id' => $movie->id,
                    'score' => rand(1, 5),
                ]);
            }

            // Actualizar promedio de la película
            $movie->updateAverageRating();
        }
    }
}
