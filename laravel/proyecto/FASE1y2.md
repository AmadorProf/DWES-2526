# Proyecto Laravel - Fases 1 y 2: Código Completo

## FASE 1: CONFIGURACIÓN INICIAL Y ESTRUCTURA

### 1.1 Instalación y Configuración de Laravel

```bash
# Crear nuevo proyecto Laravel
composer create-project laravel/laravel gestion-peliculas

# Entrar al directorio
cd gestion-peliculas

# Generar clave de aplicación
php artisan key:generate
```

### 1.2 Configuración del archivo `.env`

```env
APP_NAME="Gestión de Películas"
APP_ENV=local
APP_KEY=base64:TU_CLAVE_GENERADA
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_peliculas
DB_USERNAME=root
DB_PASSWORD=
```

### 1.3 Crear Base de Datos

```sql
CREATE DATABASE gestion_peliculas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 1.4 Configuración de Git

```bash
# Inicializar repositorio
git init

# Primer commit
git add .
git commit -m "Initial Laravel installation"

# Conectar con GitHub
git remote add origin https://github.com/tu-usuario/gestion-peliculas.git
git branch -M main
git push -u origin main
```

### 1.5 Migración de Users (modificada)

**Archivo**: `database/migrations/2014_10_12_000000_create_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

### 1.6 Migración de Movies (Contenido Principal)

**Crear migración**:
```bash
php artisan make:migration create_movies_table
```

**Archivo**: `database/migrations/xxxx_xx_xx_create_movies_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('director');
            $table->year('year');
            $table->integer('duration'); // en minutos
            $table->string('genre');
            $table->text('synopsis');
            $table->string('cast')->nullable();
            $table->string('country')->nullable();
            $table->string('poster')->nullable();
            $table->string('age_rating')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
```

### 1.7 Migración de Ratings

**Crear migración**:
```bash
php artisan make:migration create_ratings_table
```

**Archivo**: `database/migrations/xxxx_xx_xx_create_ratings_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('score'); // 1-5
            $table->timestamps();
            
            // Evitar valoraciones duplicadas
            $table->unique(['user_id', 'movie_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
```

### 1.8 Migración de Reviews

**Crear migración**:
```bash
php artisan make:migration create_reviews_table
```

**Archivo**: `database/migrations/xxxx_xx_xx_create_reviews_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->string('title', 100)->nullable();
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
```

### 1.9 Ejecutar Migraciones

```bash
# Ejecutar todas las migraciones
php artisan migrate

# Si necesitas rehacer las migraciones
php artisan migrate:fresh

# Para rollback
php artisan migrate:rollback
```

---

## FASE 2: CRUD DEL CONTENIDO PRINCIPAL

### 2.1 Modelo Movie con Relaciones

**Crear modelo**:
```bash
php artisan make:model Movie
```

**Archivo**: `app/Models/Movie.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'director',
        'year',
        'duration',
        'genre',
        'synopsis',
        'cast',
        'country',
        'poster',
        'age_rating',
        'average_rating'
    ];

    // Relación: Una película pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Una película tiene muchas valoraciones
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Relación: Una película tiene muchas reseñas
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Método para actualizar la valoración promedio
    public function updateAverageRating()
    {
        $average = $this->ratings()->avg('score');
        $this->average_rating = round($average, 2);
        $this->save();
    }
}
```

### 2.2 Actualizar Modelo User

**Archivo**: `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relación: Un usuario tiene muchas películas
    public function movies()
    {
        return $this->hasMany(Movie::class);
    }

    // Relación: Un usuario tiene muchas valoraciones
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Relación: Un usuario tiene muchas reseñas
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Método helper para verificar si es admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Método helper para verificar si es usuario normal
    public function isUser()
    {
        return $this->role === 'user';
    }
}
```

### 2.3 Resource Controller para Movies

**Crear controlador**:
```bash
php artisan make:controller MovieController --resource
```

**Archivo**: `app/Http/Controllers/MovieController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    // Aplicar middleware de autenticación excepto en index y show
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    // Mostrar listado de películas
    public function index()
    {
        $movies = Movie::with('user')
            ->latest()
            ->paginate(15);
            
        return view('movies.index', compact('movies'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('movies.create');
    }

    // Guardar nueva película
    public function store(StoreMovieRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        // Gestión de imagen
        if ($request->hasFile('poster')) {
            $data['poster'] = $request->file('poster')->store('posters', 'public');
        }

        Movie::create($data);

        return redirect()->route('movies.index')
            ->with('success', '¡Película creada exitosamente!');
    }

    // Mostrar detalle de película
    public function show(Movie $movie)
    {
        $movie->load(['user', 'ratings', 'reviews.user']);
        
        return view('movies.show', compact('movie'));
    }

    // Mostrar formulario de edición
    public function edit(Movie $movie)
    {
        // Verificar autorización
        if (auth()->id() !== $movie->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'No autorizado');
        }

        return view('movies.edit', compact('movie'));
    }

    // Actualizar película
    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        // Verificar autorización
        if (auth()->id() !== $movie->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'No autorizado');
        }

        $data = $request->validated();

        // Gestión de imagen
        if ($request->hasFile('poster')) {
            // Eliminar imagen anterior si existe
            if ($movie->poster) {
                Storage::disk('public')->delete($movie->poster);
            }
            $data['poster'] = $request->file('poster')->store('posters', 'public');
        }

        $movie->update($data);

        return redirect()->route('movies.show', $movie)
            ->with('success', 'Película actualizada exitosamente');
    }

    // Eliminar película
    public function destroy(Movie $movie)
    {
        // Verificar autorización
        if (auth()->id() !== $movie->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'No autorizado');
        }

        // Eliminar imagen si existe
        if ($movie->poster) {
            Storage::disk('public')->delete($movie->poster);
        }

        $movie->delete();

        return redirect()->route('movies.index')
            ->with('success', 'Película eliminada exitosamente');
    }
}
```

### 2.4 Form Requests para Validación

**Crear Form Requests**:
```bash
php artisan make:request StoreMovieRequest
php artisan make:request UpdateMovieRequest
```

**Archivo**: `app/Http/Requests/StoreMovieRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ya controlamos con middleware
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'director' => 'required|string|max:255',
            'year' => 'required|integer|min:1895|max:' . (date('Y') + 5),
            'duration' => 'required|integer|min:1|max:500',
            'genre' => 'required|string|max:100',
            'synopsis' => 'required|string|min:10|max:2000',
            'cast' => 'nullable|string|max:500',
            'country' => 'nullable|string|max:100',
            'age_rating' => 'nullable|string|max:10',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede superar los 255 caracteres',
            'director.required' => 'El director es obligatorio',
            'year.required' => 'El año es obligatorio',
            'year.min' => 'El año no puede ser anterior a 1895',
            'year.max' => 'El año no puede ser mayor a ' . (date('Y') + 5),
            'duration.required' => 'La duración es obligatoria',
            'duration.min' => 'La duración debe ser al menos 1 minuto',
            'genre.required' => 'El género es obligatorio',
            'synopsis.required' => 'La sinopsis es obligatoria',
            'synopsis.min' => 'La sinopsis debe tener al menos 10 caracteres',
            'synopsis.max' => 'La sinopsis no puede superar los 2000 caracteres',
            'poster.image' => 'El archivo debe ser una imagen',
            'poster.mimes' => 'La imagen debe ser jpg, jpeg, png o gif',
            'poster.max' => 'La imagen no puede superar los 2MB',
        ];
    }
}
```

**Archivo**: `app/Http/Requests/UpdateMovieRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'director' => 'required|string|max:255',
            'year' => 'required|integer|min:1895|max:' . (date('Y') + 5),
            'duration' => 'required|integer|min:1|max:500',
            'genre' => 'required|string|max:100',
            'synopsis' => 'required|string|min:10|max:2000',
            'cast' => 'nullable|string|max:500',
            'country' => 'nullable|string|max:100',
            'age_rating' => 'nullable|string|max:10',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede superar los 255 caracteres',
            'director.required' => 'El director es obligatorio',
            'year.required' => 'El año es obligatorio',
            'year.min' => 'El año no puede ser anterior a 1895',
            'year.max' => 'El año no puede ser mayor a ' . (date('Y') + 5),
            'duration.required' => 'La duración es obligatoria',
            'duration.min' => 'La duración debe ser al menos 1 minuto',
            'genre.required' => 'El género es obligatorio',
            'synopsis.required' => 'La sinopsis es obligatoria',
            'synopsis.min' => 'La sinopsis debe tener al menos 10 caracteres',
            'synopsis.max' => 'La sinopsis no puede superar los 2000 caracteres',
            'poster.image' => 'El archivo debe ser una imagen',
            'poster.mimes' => 'La imagen debe ser jpg, jpeg, png o gif',
            'poster.max' => 'La imagen no puede superar los 2MB',
        ];
    }
}
```

### 2.5 Rutas (routes/web.php)

```php
<?php

use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('movies.index');
});

// Rutas de películas (resource)
Route::resource('movies', MovieController::class);

require __DIR__.'/auth.php';
```

### 2.6 Configurar Storage

```bash
# Crear enlace simbólico para acceder a las imágenes públicamente
php artisan storage:link
```

### 2.7 Vista: Layout Principal

**Archivo**: `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestión de Películas')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('movies.index') }}">
                <i class="fas fa-film"></i> CineHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('movies.index') }}">Películas</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('movies.create') }}">Nueva Película</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Mi Perfil</a></li>
                                @if(Auth::user()->isAdmin())
                                    <li><a class="dropdown-item" href="#">Panel Admin</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Mensajes Flash -->
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Contenido Principal -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2025 CineHub - Gestión de Películas</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
```

### 2.8 Vista: Index (Listado)

**Archivo**: `resources/views/movies/index.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Películas')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1><i class="fas fa-film"></i> Catálogo de Películas</h1>
        </div>
        <div class="col-md-6 text-end">
            @auth
                <a href="{{ route('movies.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Película
                </a>
            @endauth
        </div>
    </div>

    @if($movies->count() > 0)
        <div class="row">
            @foreach($movies as $movie)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($movie->poster)
                            <img src="{{ asset('storage/' . $movie->poster) }}" 
                                 class="card-img-top" 
                                 alt="{{ $movie->title }}"
                                 style="height: 400px; object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                 style="height: 400px;">
                                <i class="fas fa-film fa-5x"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $movie->title }}</h5>
                            <p class="card-text text-muted">
                                <i class="fas fa-user"></i> {{ $movie->director }} | 
                                <i class="fas fa-calendar"></i> {{ $movie->year }}
                            </p>
                            <p class="card-text">
                                <span class="badge bg-info">{{ $movie->genre }}</span>
                                <span class="badge bg-secondary">{{ $movie->duration }} min</span>
                            </p>
                            <div class="mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($movie->average_rating))
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                                <span class="ms-2">{{ number_format($movie->average_rating, 1) }}</span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="{{ route('movies.show', $movie) }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center">
            {{ $movies->links() }}
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <h4>No hay películas registradas</h4>
            <p>Sé el primero en añadir una película al catálogo</p>
            @auth
                <a href="{{ route('movies.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Añadir Primera Película
                </a>
            @endauth
        </div>
    @endif
</div>
@endsection
```

### 2.9 Vista: Create (Formulario de Creación)

**Archivo**: `resources/views/movies/create.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Nueva Película')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Añadir Nueva Película</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('movies.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Título -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Título *</label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Director -->
                        <div class="mb-3">
                            <label for="director" class="form-label">Director *</label>
                            <input type="text" 
                                   class="form-control @error('director') is-invalid @enderror" 
                                   id="director" 
                                   name="director" 
                                   value="{{ old('director') }}" 
                                   required>
                            @error('director')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Año y Duración -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="year" class="form-label">Año *</label>
                                <input type="number" 
                                       class="form-control @error('year') is-invalid @enderror" 
                                       id="year" 
                                       name="year" 
                                       value="{{ old('year') }}" 
                                       min="1895" 
                                       max="{{ date('Y') + 5 }}" 
                                       required>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Duración (minutos) *</label>
                                <input type="number" 
                                       class="form-control @error('duration') is-invalid @enderror" 
                                       id="duration" 
                                       name="duration" 
                                       value="{{ old('duration') }}" 
                                       min="1" 
                                       required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Género -->
                        <div class="mb-3">
                            <label for="genre" class="form-label">Género *</label>
                            <select class="form-select @error('genre') is-invalid @enderror" 
                                    id="genre" 
                                    name="genre" 
                                    required>
                                <option value="">Seleccionar...</option>
                                <option value="Acción" {{ old('genre') == 'Acción' ? 'selected' : '' }}>Acción</option>
                                <option value="Comedia" {{ old('genre') == 'Comedia' ? 'selected' : '' }}>Comedia</option>
                                <option value="Drama" {{ old('genre') == 'Drama' ? 'selected' : '' }}>Drama</option>
                                <option value="Terror" {{ old('genre') == 'Terror' ? 'selected' : '' }}>Terror</option>
                                <option value="Ciencia Ficción" {{ old('genre') == 'Ciencia Ficción' ? 'selected' : '' }}>Ciencia Ficción</option>
                                <option value="Fantasía" {{ old('genre') == 'Fantasía' ? 'selected' : '' }}>Fantasía</option>
                                <option value="Romance" {{ old('genre') == 'Romance' ? 'selected' : '' }}>Romance</option>
                                <option value="Thriller" {{ old('genre') == 'Thriller' ? 'selected' : '' }}>Thriller</option>
                                <option value="Animación" {{ old('genre') == 'Animación' ? 'selected' : '' }}>Animación</option>
                            </select>
                            @error('genre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sinopsis -->
                        <div class="mb-3">
                            <label for="synopsis" class="form-label">Sinopsis *</label>
                            <textarea class="form-control @error('synopsis') is-invalid @enderror" 
                                      id="synopsis" 
                                      name="synopsis" 
                                      rows="4" 
                                      required>{{ old('synopsis') }}</textarea>
                            @error('synopsis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reparto y País -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cast" class="form-label">Reparto Principal</label>
                                <input type="text" 
                                       class="form-control @error('cast') is-invalid @enderror" 
                                       id="cast" 
                                       name="cast" 
                                       value="{{ old('cast') }}"
                                       placeholder="Actor 1, Actor 2, Actor 3...">
                                @error('cast')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">País</label>
                                <input type="text" 
                                       class="form-control @error('country') is-invalid @enderror" 
                                       id="country" 
                                       name="country" 
                                       value="{{ old('country') }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Clasificación por edad -->
                        <div class="mb-3">
                            <label for="age_rating" class="form-label">Clasificación por Edad</label>
                            <select class="form-select @error('age_rating') is-invalid @enderror" 
                                    id="age_rating" 
                                    name="age_rating">
                                <option value="">Seleccionar...</option>
                                <option value="ATP" {{ old('age_rating') == 'ATP' ? 'selected' : '' }}>ATP (Todo Público)</option>
                                <option value="+13" {{ old('age_rating') == '+13' ? 'selected' : '' }}>+13</option>
                                <option value="+16" {{ old('age_rating') == '+16' ? 'selected' : '' }}>+16</option>
                                <option value="+18" {{ old('age_rating') == '+18' ? 'selected' : '' }}>+18</option>
                            </select>
                            @error('age_rating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Póster -->
                        <div class="mb-3">
                            <label for="poster" class="form-label">Póster</label>
                            <input type="file" 
                                   class="form-control @error('poster') is-invalid @enderror" 
                                   id="poster" 
                                   name="poster" 
                                   accept="image/*">
                            <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG, GIF. Máximo 2MB.</small>
                            @error('poster')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('movies.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Película
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### 2.10 Vista: Edit (Formulario de Edición)

**Archivo**: `resources/views/movies/edit.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Editar Película')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Película</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('movies.update', $movie) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Título -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Título *</label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $movie->title) }}" 
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Director -->
                        <div class="mb-3">
                            <label for="director" class="form-label">Director *</label>
                            <input type="text" 
                                   class="form-control @error('director') is-invalid @enderror" 
                                   id="director" 
                                   name="director" 
                                   value="{{ old('director', $movie->director) }}" 
                                   required>
                            @error('director')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Año y Duración -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="year" class="form-label">Año *</label>
                                <input type="number" 
                                       class="form-control @error('year') is-invalid @enderror" 
                                       id="year" 
                                       name="year" 
                                       value="{{ old('year', $movie->year) }}" 
                                       min="1895" 
                                       max="{{ date('Y') + 5 }}" 
                                       required>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Duración (minutos) *</label>
                                <input type="number" 
                                       class="form-control @error('duration') is-invalid @enderror" 
                                       id="duration" 
                                       name="duration" 
                                       value="{{ old('duration', $movie->duration) }}" 
                                       min="1" 
                                       required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Género -->
                        <div class="mb-3">
                            <label for="genre" class="form-label">Género *</label>
                            <select class="form-select @error('genre') is-invalid @enderror" 
                                    id="genre" 
                                    name="genre" 
                                    required>
                                <option value="">Seleccionar...</option>
                                <option value="Acción" {{ old('genre', $movie->genre) == 'Acción' ? 'selected' : '' }}>Acción</option>
                                <option value="Comedia" {{ old('genre', $movie->genre) == 'Comedia' ? 'selected' : '' }}>Comedia</option>
                                <option value="Drama" {{ old('genre', $movie->genre) == 'Drama' ? 'selected' : '' }}>Drama</option>
                                <option value="Terror" {{ old('genre', $movie->genre) == 'Terror' ? 'selected' : '' }}>Terror</option>
                                <option value="Ciencia Ficción" {{ old('genre', $movie->genre) == 'Ciencia Ficción' ? 'selected' : '' }}>Ciencia Ficción</option>
                                <option value="Fantasía" {{ old('genre', $movie->genre) == 'Fantasía' ? 'selected' : '' }}>Fantasía</option>
                                <option value="Romance" {{ old('genre', $movie->genre) == 'Romance' ? 'selected' : '' }}>Romance</option>
                                <option value="Thriller" {{ old('genre', $movie->genre) == 'Thriller' ? 'selected' : '' }}>Thriller</option>
                                <option value="Animación" {{ old('genre', $movie->genre) == 'Animación' ? 'selected' : '' }}>Animación</option>
                            </select>
                            @error('genre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sinopsis -->
                        <div class="mb-3">
                            <label for="synopsis" class="form-label">Sinopsis *</label>
                            <textarea class="form-control @error('synopsis') is-invalid @enderror" 
                                      id="synopsis" 
                                      name="synopsis" 
                                      rows="4" 
                                      required>{{ old('synopsis', $movie->synopsis) }}</textarea>
                            @error('synopsis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reparto y País -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cast" class="form-label">Reparto Principal</label>
                                <input type="text" 
                                       class="form-control @error('cast') is-invalid @enderror" 
                                       id="cast" 
                                       name="cast" 
                                       value="{{ old('cast', $movie->cast) }}"
                                       placeholder="Actor 1, Actor 2, Actor 3...">
                                @error('cast')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">País</label>
                                <input type="text" 
                                       class="form-control @error('country') is-invalid @enderror" 
                                       id="country" 
                                       name="country" 
                                       value="{{ old('country', $movie->country) }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Clasificación por edad -->
                        <div class="mb-3">
                            <label for="age_rating" class="form-label">Clasificación por Edad</label>
                            <select class="form-select @error('age_rating') is-invalid @enderror" 
                                    id="age_rating" 
                                    name="age_rating">
                                <option value="">Seleccionar...</option>
                                <option value="ATP" {{ old('age_rating', $movie->age_rating) == 'ATP' ? 'selected' : '' }}>ATP (Todo Público)</option>
                                <option value="+13" {{ old('age_rating', $movie->age_rating) == '+13' ? 'selected' : '' }}>+13</option>
                                <option value="+16" {{ old('age_rating', $movie->age_rating) == '+16' ? 'selected' : '' }}>+16</option>
                                <option value="+18" {{ old('age_rating', $movie->age_rating) == '+18' ? 'selected' : '' }}>+18</option>
                            </select>
                            @error('age_rating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Póster actual -->
                        @if($movie->poster)
                            <div class="mb-3">
                                <label class="form-label">Póster Actual</label>
                                <div>
                                    <img src="{{ asset('storage/' . $movie->poster) }}" 
                                         alt="{{ $movie->title }}" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px;">
                                </div>
                            </div>
                        @endif

                        <!-- Nuevo póster -->
                        <div class="mb-3">
                            <label for="poster" class="form-label">Cambiar Póster</label>
                            <input type="file" 
                                   class="form-control @error('poster') is-invalid @enderror" 
                                   id="poster" 
                                   name="poster" 
                                   accept="image/*">
                            <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG, GIF. Máximo 2MB.</small>
                            @error('poster')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('movies.show', $movie) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Película
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### 2.11 Vista: Show (Detalle de Película)

**Archivo**: `resources/views/movies/show.blade.php`

```blade
@extends('layouts.app')

@section('title', $movie->title)

@section('content')
<div class="container">
    <!-- Información Principal -->
    <div class="row mb-4">
        <div class="col-md-4">
            @if($movie->poster)
                <img src="{{ asset('storage/' . $movie->poster) }}" 
                     class="img-fluid rounded shadow" 
                     alt="{{ $movie->title }}">
            @else
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" 
                     style="height: 500px;">
                    <i class="fas fa-film fa-5x"></i>
                </div>
            @endif
        </div>
        <div class="col-md-8">
            <h1 class="mb-3">{{ $movie->title }}</h1>
            
            <!-- Valoración -->
            <div class="mb-3">
                <h4>
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($movie->average_rating))
                            <i class="fas fa-star text-warning"></i>
                        @else
                            <i class="far fa-star text-warning"></i>
                        @endif
                    @endfor
                    <span class="ms-2">{{ number_format($movie->average_rating, 1) }}/5</span>
                    <small class="text-muted">({{ $movie->ratings->count() }} valoraciones)</small>
                </h4>
            </div>

            <!-- Información General -->
            <div class="mb-4">
                <p class="mb-2"><strong><i class="fas fa-user-tie"></i> Director:</strong> {{ $movie->director }}</p>
                <p class="mb-2"><strong><i class="fas fa-calendar"></i> Año:</strong> {{ $movie->year }}</p>
                <p class="mb-2"><strong><i class="fas fa-clock"></i> Duración:</strong> {{ $movie->duration }} minutos</p>
                <p class="mb-2"><strong><i class="fas fa-theater-masks"></i> Género:</strong> 
                    <span class="badge bg-info">{{ $movie->genre }}</span>
                </p>
                @if($movie->cast)
                    <p class="mb-2"><strong><i class="fas fa-users"></i> Reparto:</strong> {{ $movie->cast }}</p>
                @endif
                @if($movie->country)
                    <p class="mb-2"><strong><i class="fas fa-globe"></i> País:</strong> {{ $movie->country }}</p>
                @endif
                @if($movie->age_rating)
                    <p class="mb-2"><strong><i class="fas fa-exclamation-triangle"></i> Clasificación:</strong> 
                        <span class="badge bg-warning text-dark">{{ $movie->age_rating }}</span>
                    </p>
                @endif
                <p class="mb-2"><strong><i class="fas fa-user"></i> Añadido por:</strong> {{ $movie->user->name }}</p>
            </div>

            <!-- Sinopsis -->
            <div class="mb-4">
                <h5><i class="fas fa-align-left"></i> Sinopsis</h5>
                <p class="text-justify">{{ $movie->synopsis }}</p>
            </div>

            <!-- Botones de Acción -->
            <div class="d-flex gap-2">
                @auth
                    @if(auth()->id() === $movie->user_id || auth()->user()->isAdmin())
                        <a href="{{ route('movies.edit', $movie) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="{{ route('movies.destroy', $movie) }}" 
                              method="POST" 
                              onsubmit="return confirm('¿Estás seguro de eliminar esta película?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    @endif
                @endauth
                <a href="{{ route('movies.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Catálogo
                </a>
            </div>
        </div>
    </div>

    <!-- Sección de Valoraciones (Placeholder para Fase 4) -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-star"></i> Valoraciones</h5>
                </div>
                <div class="card-body">
                    @auth
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            El sistema de valoraciones se implementará en la Fase 4
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-sign-in-alt"></i> 
                            <a href="{{ route('login') }}">Inicia sesión</a> para valorar esta película
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Comentarios (Placeholder para Fase 4) -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> Comentarios ({{ $movie->reviews->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($movie->reviews->count() > 0)
                        @foreach($movie->reviews as $review)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-user-circle"></i> {{ $review->user->name }}
                                        </h6>
                                        @if($review->title)
                                            <h6 class="text-primary">{{ $review->title }}</h6>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0">{{ $review->content }}</p>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No hay comentarios todavía. ¡Sé el primero en comentar!</p>
                    @endif

                    @auth
                        <div class="mt-4">
                            <h6>Añadir Comentario</h6>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                El formulario de comentarios se implementará en la Fase 4
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-sign-in-alt"></i> 
                            <a href="{{ route('login') }}">Inicia sesión</a> para comentar
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### 2.12 Seeders y Factories

**Crear Factory para Movies**:
```bash
php artisan make:factory MovieFactory
```

**Archivo**: `database/factories/MovieFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    public function definition(): array
    {
        $genres = ['Acción', 'Comedia', 'Drama', 'Terror', 'Ciencia Ficción', 'Fantasía', 'Romance', 'Thriller', 'Animación'];
        $ratings = ['ATP', '+13', '+16', '+18'];

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'director' => fake()->name(),
            'year' => fake()->numberBetween(1980, 2025),
            'duration' => fake()->numberBetween(80, 180),
            'genre' => fake()->randomElement($genres),
            'synopsis' => fake()->paragraph(4),
            'cast' => fake()->name() . ', ' . fake()->name() . ', ' . fake()->name(),
            'country' => fake()->country(),
            'age_rating' => fake()->randomElement($ratings),
            'poster' => null, // Se puede añadir lógica para generar imágenes de placeholder
            'average_rating' => fake()->randomFloat(2, 0, 5),
        ];
    }
}
```

**Crear Seeder para Users**:
```bash
php artisan make:seeder UserSeeder
```

**Archivo**: `database/seeders/UserSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario Administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Usuarios normales
        User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Carlos López',
            'email' => 'carlos@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Generar 5 usuarios adicionales aleatorios
        User::factory(5)->create();
    }
}
```

**Crear Seeder para Movies**:
```bash
php artisan make:seeder MovieSeeder
```

**Archivo**: `database/seeders/MovieSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        // Películas de ejemplo con datos reales
        $movies = [
            [
                'title' => 'El Padrino',
                'director' => 'Francis Ford Coppola',
                'year' => 1972,
                'duration' => 175,
                'genre' => 'Drama',
                'synopsis' => 'Don Vito Corleone, el patriarca de una de las cinco familias de la mafia que gobiernan Nueva York, decide ceder su puesto a su hijo Michael.',
                'cast' => 'Marlon Brando, Al Pacino, James Caan',
                'country' => 'Estados Unidos',
                'age_rating' => '+16',
                'average_rating' => 4.9,
            ],
            [
                'title' => 'Pulp Fiction',
                'director' => 'Quentin Tarantino',
                'year' => 1994,
                'duration' => 154,
                'genre' => 'Thriller',
                'synopsis' => 'Historias cruzadas de criminales de Los Ángeles: dos asesinos a sueldo, un boxeador y una pareja de asaltantes.',
                'cast' => 'John Travolta, Uma Thurman, Samuel L. Jackson',
                'country' => 'Estados Unidos',
                'age_rating' => '+18',
                'average_rating' => 4.7,
            ],
            [
                'title' => 'El Caballero Oscuro',
                'director' => 'Christopher Nolan',
                'year' => 2008,
                'duration' => 152,
                'genre' => 'Acción',
                'synopsis' => 'Batman tiene que mantener el equilibrio entre el heroísmo y el vigilantismo para pelear contra un villano conocido como el Joker.',
                'cast' => 'Christian Bale, Heath Ledger, Aaron Eckhart',
                'country' => 'Estados Unidos',
                'age_rating' => '+13',
                'average_rating' => 4.8,
            ],
            [
                'title' => 'Forrest Gump',
                'director' => 'Robert Zemeckis',
                'year' => 1994,
                'duration' => 142,
                'genre' => 'Drama',
                'synopsis' => 'La historia de un hombre con un coeficiente intelectual bajo que presencia y participa en eventos históricos del siglo XX.',
                'cast' => 'Tom Hanks, Robin Wright, Gary Sinise',
                'country' => 'Estados Unidos',
                'age_rating' => 'ATP',
                'average_rating' => 4.6,
            ],
            [
                'title' => 'Inception',
                'director' => 'Christopher Nolan',
                'year' => 2010,
                'duration' => 148,
                'genre' => 'Ciencia Ficción',
                'synopsis' => 'Un ladrón que roba secretos a través del uso de la tecnología de los sueños recibe la tarea inversa de plantar una idea en la mente de un CEO.',
                'cast' => 'Leonardo DiCaprio, Marion Cotillard, Tom Hardy',
                'country' => 'Estados Unidos',
                'age_rating' => '+13',
                'average_rating' => 4.7,
            ],
        ];

        foreach ($movies as $movieData) {
            $movieData['user_id'] = $users->random()->id;
            Movie::create($movieData);
        }

        // Generar 15 películas adicionales aleatorias
        Movie::factory(15)->create([
            'user_id' => $users->random()->id,
        ]);
    }
}
```

**Actualizar DatabaseSeeder**:

**Archivo**: `database/seeders/DatabaseSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MovieSeeder::class,
        ]);
    }
}
```

**
