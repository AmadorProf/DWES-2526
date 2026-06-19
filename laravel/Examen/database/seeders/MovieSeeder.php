<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movie;
use App\Models\User;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Películas reales de ejemplo
        $movies = [
            [
                'title' => 'El Padrino',
                'director' => 'Francis Ford Coppola',
                'year' => 1972,
                'genre' => 'Drama',
                'duration' => 175,
                'synopsis' => 'Don Vito Corleone es el respetado y temido jefe de una de las cinco familias de la mafia de Nueva York. Tiene cuatro hijos: Connie, Sonny, Fredo y Michael.',
                'cast' => 'Marlon Brando, Al Pacino, James Caan',
                'country' => 'Estados Unidos',
                'age_rating' => '+16',
                'user_id' => User::inRandomOrder()->first()->id,
            ],
            [
                'title' => 'Pulp Fiction',
                'director' => 'Quentin Tarantino',
                'year' => 1994,
                'genre' => 'Thriller',
                'duration' => 154,
                'synopsis' => 'Jules y Vincent son dos asesinos a sueldo con no demasiadas luces cuya misión es recuperar un misterioso maletín.',
                'cast' => 'John Travolta, Samuel L. Jackson, Uma Thurman',
                'country' => 'Estados Unidos',
                'age_rating' => '+18',
                'user_id' => User::inRandomOrder()->first()->id,
            ],
            [
                'title' => 'El Caballero Oscuro',
                'director' => 'Christopher Nolan',
                'year' => 2008,
                'genre' => 'Acción',
                'duration' => 152,
                'synopsis' => 'Batman tiene que mantener el equilibrio entre el heroísmo y el vigilantismo para pelear contra un vil criminal conocido como el Joker.',
                'cast' => 'Christian Bale, Heath Ledger, Aaron Eckhart',
                'country' => 'Estados Unidos',
                'age_rating' => '+13',
                'user_id' => User::inRandomOrder()->first()->id,
            ],
            [
                'title' => 'Forrest Gump',
                'director' => 'Robert Zemeckis',
                'year' => 1994,
                'genre' => 'Drama',
                'duration' => 142,
                'synopsis' => 'Forrest Gump es un hombre con un coeficiente intelectual bajo que logra hacer cosas increíbles en su vida.',
                'cast' => 'Tom Hanks, Robin Wright, Gary Sinise',
                'country' => 'Estados Unidos',
                'age_rating' => 'ATP',
                'user_id' => User::inRandomOrder()->first()->id,
            ],
            [
                'title' => 'Matrix',
                'director' => 'Lana Wachowski, Lilly Wachowski',
                'year' => 1999,
                'genre' => 'Ciencia Ficción',
                'duration' => 136,
                'synopsis' => 'Un programador descubre que la realidad en la que vive es una simulación creada por máquinas.',
                'cast' => 'Keanu Reeves, Laurence Fishburne, Carrie-Anne Moss',
                'country' => 'Estados Unidos',
                'age_rating' => '+13',
                'user_id' => User::inRandomOrder()->first()->id,
            ],
            [
                'title' => 'Inception',
                'director' => 'Christopher Nolan',
                'year' => 2010,
                'genre' => 'Ciencia Ficción',
                'duration' => 148,
                'synopsis' => 'Un ladrón que roba secretos del subconsciente es contratado para implantar una idea en la mente de un ejecutivo.',
                'cast' => 'Leonardo DiCaprio, Joseph Gordon-Levitt, Ellen Page',
                'country' => 'Estados Unidos',
                'age_rating' => '+13',
                'user_id' => User::inRandomOrder()->first()->id,
            ],
            [
                'title' => 'Gladiator',
                'director' => 'Ridley Scott',
                'year' => 2000,
                'genre' => 'Acción',
                'duration' => 155,
                'synopsis' => 'Un general romano es traicionado y su familia asesinada. Convertido en esclavo, busca venganza como gladiador.',
                'cast' => 'Russell Crowe, Joaquin Phoenix, Connie Nielsen',
                'country' => 'Estados Unidos',
                'age_rating' => '+16',
                'user_id' => User::inRandomOrder()->first()->id,
            ],
            [
                'title' => 'Interstellar',
                'director' => 'Christopher Nolan',
                'year' => 2014,
                'genre' => 'Ciencia Ficción',
                'duration' => 169,
                'synopsis' => 'Un grupo de astronautas viaja a través de un agujero de gusano en busca de un nuevo hogar para la humanidad.',
                'cast' => 'Matthew McConaughey, Anne Hathaway, Jessica Chastain',
                'country' => 'Estados Unidos',
                'age_rating' => '+13',
                'user_id' => User::inRandomOrder()->first()->id,
            ],
            [
                'title' => 'El Origen',
                'director' => 'Christopher Nolan',
                'year' => 2010,
                'genre' => 'Thriller',
                'duration' => 148,
                'synopsis' => 'Dom Cobb es un ladrón especializado en extraer secretos del subconsciente de las personas mientras sueñan.',
                'cast' => 'Leonardo DiCaprio, Marion Cotillard, Tom Hardy',
                'country' => 'Estados Unidos',
                'age_rating' => '+13',
                'user_id' => User::inRandomOrder()->first()->id,
            ],
            [
                'title' => 'Parasite',
                'director' => 'Bong Joon-ho',
                'year' => 2019,
                'genre' => 'Drama',
                'duration' => 132,
                'synopsis' => 'Una familia pobre se infiltra en la vida de una familia rica con consecuencias impredecibles.',
                'cast' => 'Song Kang-ho, Lee Sun-kyun, Cho Yeo-jeong',
                'country' => 'Corea del Sur',
                'age_rating' => '+16',
                'user_id' => User::inRandomOrder()->first()->id,
            ],
        ];

        foreach ($movies as $movieData) {
            Movie::create($movieData);
        }

        // Generar películas adicionales con factory
        Movie::factory()->count(15)->create();
    }
}
