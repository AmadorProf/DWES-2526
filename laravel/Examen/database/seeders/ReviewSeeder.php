<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Movie;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $movies = Movie::all();

        // Cada película recibe entre 0 y 3 reseñas
        foreach ($movies as $movie) {
            $numReviews = rand(0, 3);
            
            if ($numReviews > 0) {
                $usersToReview = $users->random(min($numReviews, $users->count()));

                foreach ($usersToReview as $user) {
                    Review::create([
                        'user_id' => $user->id,
                        'movie_id' => $movie->id,
                        'title' => fake()->sentence(),
                        'content' => fake()->paragraph(3),
                    ]);
                }
            }
        }
    }
}
