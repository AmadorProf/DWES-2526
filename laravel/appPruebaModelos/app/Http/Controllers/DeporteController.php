<?php

namespace App\Http\Controllers;

use App\Models\Deporte;
use Illuminate\Http\Request;

class DeporteController extends Controller
{
    /**
     * Muestra la lista de todos los deportes.
     */
    public function index()
    {
        // Obtener todos los deportes activos
        $deportes = Deporte::activo()->get();

        return view('deportes.index', compact('deportes'));
    }

    /**
     * Muestra el detalle de un deporte específico.
     */
    public function show($slug)
    {
        // Buscar el deporte por slug
        $deporte = Deporte::where('slug', $slug)->firstOrFail();

        return view('deportes.show', compact('deporte'));
    }

    /**
     * Muestra el formulario para crear un nuevo deporte.
     */
    public function create()
    {
        return view('deportes.create');
    }

    /**
     * Guarda un nuevo deporte en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:255',
            'slug' => 'required|unique:deportes|max:255',
            'descripcion_corta' => 'required',
            'contenido' => 'required',
            'jugadores' => 'required|integer',
            'origen' => 'required|max:255'
        ]);

        Deporte::create($validated);

        return redirect()->route('deportes.index')
            ->with('success', 'Deporte creado exitosamente');
    }

    /**
     * Muestra el formulario para editar un deporte.
     */
    public function edit(Deporte $deporte)
    {
        return view('deportes.edit', compact('deporte'));
    }

    /**
     * Actualiza un deporte en la base de datos.
     */
    public function update(Request $request, Deporte $deporte)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:255',
            'slug' => 'required|max:255|unique:deportes,slug,' . $deporte->id,
            'descripcion_corta' => 'required',
            'contenido' => 'required',
            'jugadores' => 'required|integer',
            'origen' => 'required|max:255'
        ]);

        $deporte->update($validated);

        return redirect()->route('deportes.index')
            ->with('success', 'Deporte actualizado exitosamente');
    }

    /**
     * Elimina un deporte de la base de datos.
     */
    public function destroy(Deporte $deporte)
    {
        $deporte->delete();

        return redirect()->route('deportes.index')
            ->with('success', 'Deporte eliminado exitosamente');
    }
}
