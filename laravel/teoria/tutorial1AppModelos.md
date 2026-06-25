# Tutorial Completo: Página Web con Laravel 12 usando Modelos y Base de Datos

Este tutorial te enseñará a crear un portal deportivo profesional usando el patrón MVC completo de Laravel.

## Índice
1. [Configuración Inicial](#paso-1-configuración-de-la-base-de-datos)
2. [Crear Modelo y Migración](#paso-2-crear-modelo-y-migración)
3. [Crear Seeder](#paso-3-crear-seeder-datos-iniciales)
4. [Crear Controlador](#paso-4-crear-controlador)
5. [Configurar Rutas](#paso-5-configurar-rutas)
6. [Crear Vistas](#paso-6-crear-vistas)
7. [CRUD Completo (Bonus)](#bonus-crud-completo)

---

## Paso 1: Configuración de la Base de Datos

### 1.1 Configurar el archivo `.env`

Abre el archivo `.env` en la raíz de tu proyecto y configura la base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_deportivo
DB_USERNAME=root
DB_PASSWORD=
```

### 1.2 Crear la base de datos

Abre phpMyAdmin o tu gestor de bases de datos y crea una base de datos llamada `portal_deportivo`.

O desde la terminal MySQL:

```sql
CREATE DATABASE portal_deportivo;
```

---

## Paso 2: Crear Modelo y Migración

### 2.1 Generar el Modelo con Migración

Ejecuta este comando para crear el modelo `Deporte` junto con su migración:

```bash
php artisan make:model Deporte -m
```

El flag `-m` crea automáticamente la migración asociada.

### 2.2 Editar la Migración

Abre el archivo generado en `database/migrations/XXXX_XX_XX_create_deportes_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deportes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion_corta');
            $table->text('contenido');
            $table->integer('jugadores');
            $table->string('origen');
            $table->string('imagen')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deportes');
    }
};
```

### 2.3 Ejecutar la Migración

```bash
php artisan migrate
```

### 2.4 Configurar el Modelo

Edita el archivo `app/Models/Deporte.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deporte extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion_corta',
        'contenido',
        'jugadores',
        'origen',
        'imagen',
        'activo'
    ];

    /**
     * Los atributos que deben ser casteados.
     */
    protected $casts = [
        'activo' => 'boolean',
        'jugadores' => 'integer'
    ];

    /**
     * Scope para obtener solo deportes activos
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
```

---

## Paso 3: Crear Seeder (Datos Iniciales)

### 3.1 Generar el Seeder

```bash
php artisan make:seeder DeporteSeeder
```

### 3.2 Editar el Seeder

Abre `database/seeders/DeporteSeeder.php`:

```php
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
```

### 3.3 Registrar el Seeder

Edita `database/seeders/DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DeporteSeeder::class,
        ]);
    }
}
```

### 3.4 Ejecutar el Seeder

```bash
php artisan db:seed
```

O si quieres refrescar la base de datos y ejecutar todos los seeders:

```bash
php artisan migrate:fresh --seed
```

---

## Paso 4: Crear Controlador

### 4.1 Generar el Controlador

```bash
php artisan make:controller DeporteController --resource
```

El flag `--resource` crea automáticamente todos los métodos CRUD.

### 4.2 Editar el Controlador

Abre `app/Http/Controllers/DeporteController.php`:

```php
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
```

---

## Paso 5: Configurar Rutas

Edita el archivo `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeporteController;

// Ruta principal - Lista de deportes
Route::get('/', [DeporteController::class, 'index'])->name('home');

// Rutas del recurso deportes
Route::resource('deportes', DeporteController::class);

// Ruta personalizada para mostrar deporte por slug
Route::get('/deporte/{slug}', [DeporteController::class, 'show'])->name('deportes.slug');
```

---

## Paso 6: Crear Vistas

### 6.1 Layout Principal

Crea `resources/views/layouts/app.blade.php`:

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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav .nav-links a {
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

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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
            <div class="nav-links">
                <a href="{{ route('home') }}">Inicio</a>
                <a href="{{ route('deportes.index') }}">Deportes</a>
            </div>
            <div>
                <a href="{{ route('deportes.create') }}" class="btn btn-success">+ Nuevo Deporte</a>
            </div>
        </nav>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>

        <footer>
            <p>&copy; 2025 Portal Deportivo - Laravel 12 con Modelos</p>
        </footer>
    </div>
</body>
</html>
```

### 6.2 Vista Index (Lista de Deportes)

Crea la carpeta y archivo `resources/views/deportes/index.blade.php`:

```bash
mkdir resources/views/deportes
```

```blade
@extends('layouts.app')

@section('title', 'Deportes - Portal Deportivo')

@section('content')
<style>
    .intro {
        text-align: center;
        margin-bottom: 40px;
    }

    .intro h1 {
        color: #333;
        margin-bottom: 15px;
    }

    .deportes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
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
        position: relative;
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

    .deporte-info {
        background: white;
        padding: 10px;
        border-radius: 5px;
        margin: 15px 0;
        font-size: 0.9rem;
        color: #666;
    }

    .card-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 15px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .empty-state h2 {
        color: #999;
        margin-bottom: 20px;
    }
</style>

<div class="intro">
    <h1>Descubre Nuestros Deportes</h1>
    <p>Explora información detallada sobre diversos deportes</p>
    <p><strong>Total de deportes: {{ $deportes->count() }}</strong></p>
</div>

@if($deportes->count() > 0)
    <div class="deportes-grid">
        @foreach($deportes as $deporte)
        <div class="deporte-card">
            <h2>{{ $deporte->nombre }}</h2>
            <p>{{ $deporte->descripcion_corta }}</p>
            
            <div class="deporte-info">
                <strong>Jugadores:</strong> {{ $deporte->jugadores }}<br>
                <strong>Origen:</strong> {{ $deporte->origen }}
            </div>

            <div class="card-actions">
                <a href="{{ route('deportes.slug', $deporte->slug) }}" class="btn btn-primary">Ver más</a>
                <a href="{{ route('deportes.edit', $deporte->id) }}" class="btn btn-success">Editar</a>
                
                <form action="{{ route('deportes.destroy', $deporte->id) }}" method="POST" style="display: inline;" 
                      onsubmit="return confirm('¿Estás seguro de eliminar este deporte?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <h2>No hay deportes registrados</h2>
        <p>Comienza agregando tu primer deporte</p>
        <a href="{{ route('deportes.create') }}" class="btn btn-primary">Crear Deporte</a>
    </div>
@endif
@endsection
```

### 6.3 Vista Show (Detalle del Deporte)

Crea `resources/views/deportes/show.blade.php`:

```blade
@extends('layouts.app')

@section('title', $deporte->nombre . ' - Portal Deportivo')

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

    .actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }
</style>

<div class="detalle-deporte">
    <h1>{{ $deporte->nombre }}</h1>
    
    <div class="info-box">
        <p>{{ $deporte->contenido }}</p>
        
        <div class="datos">
            <div class="dato-item">
                <strong>Jugadores por equipo:</strong>
                {{ $deporte->jugadores }}
            </div>
            <div class="dato-item">
                <strong>Origen:</strong>
                {{ $deporte->origen }}
            </div>
            <div class="dato-item">
                <strong>Creado:</strong>
                {{ $deporte->created_at->format('d/m/Y') }}
            </div>
            <div class="dato-item">
                <strong>Actualizado:</strong>
                {{ $deporte->updated_at->format('d/m/Y') }}
            </div>
        </div>
    </div>

    <div class="actions">
        <a href="{{ route('home') }}" class="btn btn-primary">← Volver al inicio</a>
        <a href="{{ route('deportes.edit', $deporte->id) }}" class="btn btn-success">Editar</a>
        
        <form action="{{ route('deportes.destroy', $deporte->id) }}" method="POST" 
              onsubmit="return confirm('¿Estás seguro de eliminar este deporte?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Eliminar</button>
        </form>
    </div>
</div>
@endsection
```

---

## BONUS: CRUD Completo

### Vista Create (Crear Deporte)

Crea `resources/views/deportes/create.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Crear Deporte')

@section('content')
<style>
    .form-container {
        max-width: 600px;
        margin: 0 auto;
        background: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
    }

    .form-container h1 {
        color: #333;
        margin-bottom: 30px;
        text-align: center;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: bold;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
    }

    .form-group textarea {
        min-height: 120px;
        resize: vertical;
    }

    .error {
        color: #dc3545;
        font-size: 0.9rem;
        margin-top: 5px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }
</style>

<div class="form-container">
    <h1>Crear Nuevo Deporte</h1>

    <form action="{{ route('deportes.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nombre">Nombre del Deporte *</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
            @error('nombre')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="slug">Slug (URL amigable) *</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug') }}" required>
            <small>Ejemplo: futbol, baloncesto, tenis</small>
            @error('slug')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="descripcion_corta">Descripción Corta *</label>
            <input type="text" id="descripcion_corta" name="descripcion_corta" value="{{ old('descripcion_corta') }}" required>
            @error('descripcion_corta')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="contenido">Contenido *</label>
            <textarea id="contenido" name="contenido" required>{{ old('contenido', $deporte->contenido) }}</textarea>
            @error('contenido')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="jugadores">Número de Jugadores *</label>
            <input type="number" id="jugadores" name="jugadores" value="{{ old('jugadores', $deporte->jugadores) }}" required>
            @error('jugadores')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="origen">Origen *</label>
            <input type="text" id="origen" name="origen" value="{{ old('origen', $deporte->origen) }}" required>
            @error('origen')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">Actualizar Deporte</button>
            <a href="{{ route('deportes.index') }}" class="btn btn-danger">Cancelar</a>
        </div>
    </form>
</div>
@endsection
```

---

## Paso 7: Probar la Aplicación

### 7.1 Verificar que todo está configurado

```bash
# Verificar migraciones
php artisan migrate:status

# Verificar datos en la base de datos
php artisan tinker
>>> App\Models\Deporte::count()
>>> App\Models\Deporte::all()
>>> exit
```

### 7.2 Iniciar el servidor

```bash
php artisan serve
```

### 7.3 Probar las funcionalidades

Abre tu navegador en `http://localhost:8000` y prueba:

1. ✅ Ver la lista de deportes
2. ✅ Ver el detalle de un deporte
3. ✅ Crear un nuevo deporte
4. ✅ Editar un deporte existente
5. ✅ Eliminar un deporte

---

## Resumen de Conceptos Importantes

### 1. **Modelo (Model)**
- Representa una tabla en la base de datos
- Define relaciones, validaciones y lógica de negocio
- Ubicación: `app/Models/Deporte.php`

### 2. **Migración (Migration)**
- Define la estructura de las tablas
- Permite versionar la base de datos
- Ubicación: `database/migrations/`

### 3. **Seeder**
- Puebla la base de datos con datos iniciales
- Útil para desarrollo y testing
- Ubicación: `database/seeders/`

### 4. **Controlador (Controller)**
- Maneja la lógica de la aplicación
- Conecta modelos y vistas
- Ubicación: `app/Http/Controllers/DeporteController.php`

### 5. **Rutas (Routes)**
- Define las URLs de la aplicación
- Conecta URLs con métodos del controlador
- Ubicación: `routes/web.php`

### 6. **Vistas (Views)**
- Presenta la información al usuario
- Usa Blade como motor de plantillas
- Ubicación: `resources/views/`

---

## Estructura Final del Proyecto

```
proyecto-laravel/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── DeporteController.php
│   └── Models/
│       └── Deporte.php
├── database/
│   ├── migrations/
│   │   └── XXXX_XX_XX_create_deportes_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── DeporteSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       └── deportes/
│           ├── index.blade.php
│           ├── show.blade.php
│           ├── create.blade.php
│           └── edit.blade.php
└── routes/
    └── web.php
```

---

## Comandos Útiles de Laravel

```bash
# Crear modelo con migración
php artisan make:model NombreModelo -m

# Crear controlador resource
php artisan make:controller NombreController --resource

# Crear seeder
php artisan make:seeder NombreSeeder

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Refrescar base de datos y ejecutar seeders
php artisan migrate:fresh --seed

# Ver todas las rutas
php artisan route:list

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## Diferencias entre el Tutorial SIN Modelos vs CON Modelos

| Aspecto | Sin Modelos | Con Modelos |
|---------|-------------|-------------|
| **Datos** | Hardcodeados en el controlador | Almacenados en base de datos |
| **Persistencia** | No persisten | Persisten entre sesiones |
| **Escalabilidad** | Limitada | Alta |
| **CRUD** | No disponible | Completo |
| **Mantenimiento** | Difícil | Fácil |
| **Uso profesional** | Solo prototipos | Proyectos reales |

---

## Próximos Pasos (Opcional)

1. **Agregar validaciones personalizadas**
2. **Implementar búsqueda y filtros**
3. **Agregar paginación** (`$deportes = Deporte::paginate(10)`)
4. **Subir imágenes** para cada deporte
5. **Crear relaciones** (categorías, comentarios, etc.)
6. **Implementar autenticación** (Laravel Breeze/Jetstream)
7. **Crear API REST** para los deportes

---

¡Felicidades! Has completado un CRUD completo en Laravel 12 usando el patrón MVC profesionalmente. 🎉ido" name="contenido" required>{{ old('contenido') }}</textarea>
            @error('contenido')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="jugadores">Número de Jugadores *</label>
            <input type="number" id="jugadores" name="jugadores" value="{{ old('jugadores') }}" required>
            @error('jugadores')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="origen">Origen *</label>
            <input type="text" id="origen" name="origen" value="{{ old('origen') }}" required>
            @error('origen')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">Crear Deporte</button>
            <a href="{{ route('deportes.index') }}" class="btn btn-danger">Cancelar</a>
        </div>
    </form>
</div>
@endsection
```

### Vista Edit (Editar Deporte)

Crea `resources/views/deportes/edit.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Editar ' . $deporte->nombre)

@section('content')
<style>
    .form-container {
        max-width: 600px;
        margin: 0 auto;
        background: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
    }

    .form-container h1 {
        color: #333;
        margin-bottom: 30px;
        text-align: center;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: bold;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
    }

    .form-group textarea {
        min-height: 120px;
        resize: vertical;
    }

    .error {
        color: #dc3545;
        font-size: 0.9rem;
        margin-top: 5px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }
</style>

<div class="form-container">
    <h1>Editar: {{ $deporte->nombre }}</h1>

    <form action="{{ route('deportes.update', $deporte->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre">Nombre del Deporte *</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $deporte->nombre) }}" required>
            @error('nombre')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="slug">Slug (URL amigable) *</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug', $deporte->slug) }}" required>
            @error('slug')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="descripcion_corta">Descripción Corta *</label>
            <input type="text" id="descripcion_corta" name="descripcion_corta" value="{{ old('descripcion_corta', $deporte->descripcion_corta) }}" required>
            @error('descripcion_corta')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="contenido">Contenido *</label>
            <textarea id="conten
