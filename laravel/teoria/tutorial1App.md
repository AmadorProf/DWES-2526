# Tutorial: Tu primera página web en Laravel 12 con contenido deportivo

Te voy a guiar paso a paso para crear una página web con 3 entradas sobre deportes en Laravel 12.

## Paso 1: Crear el Controlador

Primero, crea un controlador para manejar las páginas deportivas:

```bash
php artisan make:controller DeportesController
```

## Paso 2: Configurar las Rutas

Abre el archivo `routes/web.php` y añade las siguientes rutas:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeportesController;

Route::get('/', [DeportesController::class, 'index'])->name('home');
Route::get('/deportes/futbol', [DeportesController::class, 'futbol'])->name('deportes.futbol');
Route::get('/deportes/baloncesto', [DeportesController::class, 'baloncesto'])->name('deportes.baloncesto');
Route::get('/deportes/tenis', [DeportesController::class, 'tenis'])->name('deportes.tenis');
```

## Paso 3: Modificar el Controlador

Edita el archivo `app/Http/Controllers/DeportesController.php`:

```php
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
```

## Paso 4: Crear las Vistas

### 4.1 Layout Principal

Crea el archivo `resources/views/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Deportivo')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        nav {
            background: #333;
            padding: 15px;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        nav a:hover {
            background: #555;
        }

        .content {
            padding: 40px;
        }

        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🏆 Portal Deportivo</h1>
            <p>Tu fuente de información deportiva</p>
        </header>

        <nav>
            <a href="{{ route('home') }}">Inicio</a>
            <a href="{{ route('deportes.futbol') }}">Fútbol</a>
            <a href="{{ route('deportes.baloncesto') }}">Baloncesto</a>
            <a href="{{ route('deportes.tenis') }}">Tenis</a>
        </nav>

        <div class="content">
            @yield('content')
        </div>

        <footer>
            <p>&copy; 2025 Portal Deportivo - Hecho con Laravel 12</p>
        </footer>
    </div>
</body>
</html>
```

### 4.2 Vista de Inicio

Crea el archivo `resources/views/home.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Inicio - Portal Deportivo')

@section('content')
<style>
    .deportes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 20px;
    }

    .deporte-card {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .deporte-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .deporte-card h2 {
        color: #667eea;
        margin-bottom: 15px;
        font-size: 1.8rem;
    }

    .deporte-card p {
        color: #555;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .btn {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        transition: transform 0.3s;
    }

    .btn:hover {
        transform: scale(1.05);
    }

    .intro {
        text-align: center;
        margin-bottom: 40px;
    }

    .intro h1 {
        color: #333;
        margin-bottom: 15px;
    }

    .intro p {
        color: #666;
        font-size: 1.1rem;
    }
</style>

<div class="intro">
    <h1>Bienvenido al Portal Deportivo</h1>
    <p>Descubre información sobre los deportes más populares del mundo</p>
</div>

<div class="deportes-grid">
    @foreach($deportes as $deporte)
    <div class="deporte-card">
        <h2>{{ $deporte['nombre'] }}</h2>
        <p>{{ $deporte['descripcion'] }}</p>
        <a href="{{ route($deporte['ruta']) }}" class="btn">Ver más</a>
    </div>
    @endforeach
</div>
@endsection
```

### 4.3 Vista de Detalle

Crea la carpeta `resources/views/deportes` y dentro el archivo `detalle.blade.php`:

```bash
mkdir resources/views/deportes
```

```blade
@extends('layouts.app')

@section('title', $deporte['titulo'] . ' - Portal Deportivo')

@section('content')
<style>
    .detalle-deporte {
        max-width: 800px;
        margin: 0 auto;
    }

    .detalle-deporte h1 {
        color: #667eea;
        font-size: 2.5rem;
        margin-bottom: 20px;
        text-align: center;
    }

    .info-box {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 30px;
        border-radius: 10px;
        margin: 20px 0;
    }

    .info-box p {
        color: #333;
        line-height: 1.8;
        font-size: 1.1rem;
        margin-bottom: 20px;
    }

    .datos {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 30px;
    }

    .dato-item {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .dato-item strong {
        color: #667eea;
        display: block;
        margin-bottom: 10px;
    }

    .btn-volver {
        display: inline-block;
        margin-top: 30px;
        background: #333;
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .btn-volver:hover {
        background: #555;
    }
</style>

<div class="detalle-deporte">
    <h1>{{ $deporte['titulo'] }}</h1>
    
    <div class="info-box">
        <p>{{ $deporte['contenido'] }}</p>
        
        <div class="datos">
            <div class="dato-item">
                <strong>Jugadores por equipo:</strong>
                {{ $deporte['jugadores'] }}
            </div>
            <div class="dato-item">
                <strong>Origen:</strong>
                {{ $deporte['origen'] }}
            </div>
        </div>
    </div>

    <a href="{{ route('home') }}" class="btn-volver">← Volver al inicio</a>
</div>
@endsection
```

## Paso 5: Probar la Aplicación

Inicia el servidor de desarrollo:

```bash
php artisan serve
```

Abre tu navegador en `http://localhost:8000` y deberías ver tu portal deportivo funcionando.

## Resumen de Cambios

### Rutas (`routes/web.php`)
- Ruta principal `/` que muestra el listado
- 3 rutas individuales para cada deporte

### Controlador (`DeportesController.php`)
- Método `index()` para la página principal
- Métodos `futbol()`, `baloncesto()`, `tenis()` para cada deporte

### Vistas
- `layouts/app.blade.php` - Plantilla base
- `home.blade.php` - Página principal con cards
- `deportes/detalle.blade.php` - Página de detalle reutilizable

## Estructura del Proyecto

```
proyecto-laravel/
├── app/
│   └── Http/
│       └── Controllers/
│           └── DeportesController.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── deportes/
│       │   └── detalle.blade.php
│       └── home.blade.php
└── routes/
    └── web.php
```

¡Y listo! Ya tienes tu primera página web en Laravel 12 con contenido deportivo funcionando.
