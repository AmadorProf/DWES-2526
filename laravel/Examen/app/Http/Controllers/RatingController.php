<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Store or update a rating
     */
    public function store(Request $request, Movie $movie)
    {
        $request->validate([
            'score' => 'required|integer|min:1|max:5',
        ]);

        // Buscar si ya existe una valoración del usuario para esta película
        $rating = Rating::where('user_id', auth()->id())
                        ->where('movie_id', $movie->id)
                        ->first();

        if ($rating) {
            // Actualizar valoración existente
            $rating->update(['score' => $request->score]);
            $message = 'Tu valoración ha sido actualizada';
        } else {
            // Crear nueva valoración
            Rating::create([
                'user_id' => auth()->id(),
                'movie_id' => $movie->id,
                'score' => $request->score,
            ]);
            $message = 'Tu valoración ha sido guardada';
        }

        // Recalcular promedio
        $movie->updateAverageRating();

        return redirect()->route('movies.show', $movie)
            ->with('success', $message);
    }
}
