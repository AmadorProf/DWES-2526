<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Review;
use App\Http\Requests\StoreReviewRequest;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a newly created review
     */
    public function store(StoreReviewRequest $request, Movie $movie)
    {
        Review::create([
            'user_id' => auth()->id(),
            'movie_id' => $movie->id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('movies.show', $movie)
            ->with('success', 'Reseña publicada correctamente');
    }

    /**
     * Update the specified review
     */
    public function update(StoreReviewRequest $request, Review $review)
    {
        // Verificar que el usuario sea el creador
        if (auth()->id() !== $review->user_id) {
            abort(403, 'No tienes permiso para editar esta reseña');
        }

        $review->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('movies.show', $review->movie)
            ->with('success', 'Reseña actualizada correctamente');
    }

    /**
     * Remove the specified review
     */
    public function destroy(Review $review)
    {
        // Verificar que el usuario sea el creador o admin
        if (auth()->id() !== $review->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'No tienes permiso para eliminar esta reseña');
        }

        $movieId = $review->movie_id;
        $review->delete();

        return redirect()->route('movies.show', $movieId)
            ->with('success', 'Reseña eliminada correctamente');
    }
}
