<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::with('user')->latest()->paginate(15);
        return view('movies.index', compact('movies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('movies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovieRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        // Gestión de imagen
        if ($request->hasFile('poster')) {
            $data['poster'] = $request->file('poster')->store('posters', 'public');
        } else {
            $data['poster'] = 'posters/default.jpg';
        }

        Movie::create($data);

        return redirect()->route('movies.index')
            ->with('success', 'Película creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        $movie->load(['user', 'ratings.user', 'reviews.user']);
        
        $userRating = null;
        if (auth()->check()) {
            $userRating = $movie->getUserRating(auth()->id());
        }

        return view('movies.show', compact('movie', 'userRating'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie)
    {
        // Verificar que el usuario sea el creador o admin
        if (auth()->id() !== $movie->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'No tienes permiso para editar esta película');
        }

        return view('movies.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        // Verificar que el usuario sea el creador o admin
        if (auth()->id() !== $movie->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'No tienes permiso para actualizar esta película');
        }

        $data = $request->validated();

        // Gestión de nueva imagen
        if ($request->hasFile('poster')) {
            // Eliminar imagen antigua si no es la por defecto
            if ($movie->poster && $movie->poster !== 'posters/default.jpg') {
                Storage::disk('public')->delete($movie->poster);
            }
            $data['poster'] = $request->file('poster')->store('posters', 'public');
        }

        $movie->update($data);

        return redirect()->route('movies.show', $movie)
            ->with('success', 'Película actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        // Verificar que el usuario sea el creador o admin
        if (auth()->id() !== $movie->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'No tienes permiso para eliminar esta película');
        }

        // Eliminar imagen si no es la por defecto
        if ($movie->poster && $movie->poster !== 'posters/default.jpg') {
            Storage::disk('public')->delete($movie->poster);
        }

        $movie->delete();

        return redirect()->route('movies.index')
            ->with('success', 'Película eliminada correctamente');
    }
}
