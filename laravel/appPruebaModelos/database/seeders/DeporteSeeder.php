<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deporte;

class DeporteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deportes = [
            [
                'nombre' => 'Fútbol',
                'slug' => 'futbol',
                'descripcion_corta' => 'El deporte rey a nivel mundial',
                'contenido' => 'El fútbol es el deporte más popular del mundo, con millones de seguidores en todos los continentes. Se juega entre dos equipos de 11 jugadores que intentan meter el balón en la portería contraria. Es un deporte que combina habilidad técnica, táctica y resistencia física.',
                'jugadores' => 11,
                'origen' => 'Inglaterra, siglo XIX',
                'activo' => true
            ],
            [
                'nombre' => 'Baloncesto',
                'slug' => 'baloncesto',
                'descripcion_corta' => 'Intensidad y espectáculo en la cancha',
                'contenido' => 'El baloncesto es un deporte de equipo donde dos conjuntos de cinco jugadores intentan anotar puntos introduciendo un balón en la canasta del equipo contrario. Inventado por James Naismith, es uno de los deportes más dinámicos y espectaculares del mundo.',
                'jugadores' => 5,
                'origen' => 'Estados Unidos, 1891',
                'activo' => true
            ],
            [
                'nombre' => 'Tenis',
                'slug' => 'tenis',
                'descripcion_corta' => 'Elegancia y precisión en cada golpe',
                'contenido' => 'El tenis es un deporte de raqueta que se practica en un terreno llano, rectangular, dividido por una red intermedia. Puede jugarse entre dos personas (individuales) o en parejas (dobles). Requiere gran técnica, velocidad y resistencia mental.',
                'jugadores' => 2,
                'origen' => 'Francia, siglo XII',
                'activo' => true
            ],
            [
                'nombre' => 'Natación',
                'slug' => 'natacion',
                'descripcion_corta' => 'El deporte más completo',
                'contenido' => 'La natación es un deporte que consiste en el desplazamiento de una persona en el agua, sin que esta toque el suelo. Es considerado uno de los deportes más completos ya que trabaja todos los grupos musculares y mejora la capacidad cardiovascular.',
                'jugadores' => 1,
                'origen' => 'Prehistoria',
                'activo' => true
            ]
        ];

        foreach ($deportes as $deporte) {
            Deporte::create($deporte);
        }
    }
}
