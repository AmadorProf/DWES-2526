<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeportesController extends Controller
{
    public function index()
    {
        $deportes = [
            [
                'nombre' => 'Fútbol',
                'ruta' => 'deportes.futbol',
                'imagen' => 'futbol.jpg',
                'descripcion' => 'El deporte rey a nivel mundial'
            ],
            [
                'nombre' => 'Baloncesto',
                'ruta' => 'deportes.baloncesto',
                'imagen' => 'baloncesto.jpg',
                'descripcion' => 'Intensidad y espectáculo en la cancha'
            ],
            [
                'nombre' => 'Tenis',
                'ruta' => 'deportes.tenis',
                'imagen' => 'tenis.jpg',
                'descripcion' => 'Elegancia y precisión en cada golpe'
            ]
        ];

        return view('home', compact('deportes'));
    }

    public function futbol()
    {
        $deporte = [
            'titulo' => 'Fútbol',
            'contenido' => 'El fútbol es el deporte más popular del mundo, con millones de seguidores. Se juega entre dos equipos de 11 jugadores que intentan meter el balón en la portería contraria.',
            'jugadores' => 11,
            'origen' => 'Inglaterra, siglo XIX'
        ];

        return view('deportes.detalle', compact('deporte'));
    }

    public function baloncesto()
    {
        $deporte = [
            'titulo' => 'Baloncesto',
            'contenido' => 'El baloncesto es un deporte de equipo donde dos conjuntos de cinco jugadores intentan anotar puntos introduciendo un balón en la canasta del equipo contrario.',
            'jugadores' => 5,
            'origen' => 'Estados Unidos, 1891'
        ];

        return view('deportes.detalle', compact('deporte'));
    }

    public function tenis()
    {
        $deporte = [
            'titulo' => 'Tenis',
            'contenido' => 'El tenis es un deporte de raqueta que se practica en un terreno llano, rectangular, dividido por una red intermedia. Puede jugarse entre dos personas o en parejas.',
            'jugadores' => '1 o 2 por equipo',
            'origen' => 'Francia, siglo XII'
        ];

        return view('deportes.detalle', compact('deporte'));
    }
}
