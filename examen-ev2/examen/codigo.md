# SOBRE DE CÓDIGO
## Plataforma de Gestión de Música

---

# PARTE 1 — Fragmentos A a N

Coloca cada fragmento en el fichero y ubicación que indica el enunciado. Sigue el orden de los pasos.

---

## FRAGMENTO A — Migración `albums` (métodos `up` y `down`)

```php
// Método up()
Schema::create('albums', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->string('artist');
    $table->string('genre');
    $table->smallInteger('release_year');
    $table->string('record_label')->nullable();
    $table->text('description')->nullable();
    $table->string('cover')->nullable();
    $table->decimal('average_rating', 3, 2)->default(0);
    $table->timestamps();
});
```

```php
// Método down()
Schema::dropIfExists('albums');
```

---

## FRAGMENTO B — Modelo `Album`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'artist',
        'genre',
        'release_year',
        'record_label',
        'description',
        'cover',
        'average_rating',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

---

## FRAGMENTO C — Seeder `AlbumSeeder`

```php
<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Seeder;

class AlbumSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name'     => 'Usuario Demo',
            'email'    => 'demo@musica.com',
            'password' => bcrypt('password'),
        ]);

        $albums = [
            ['title' => 'Thriller',               'artist' => 'Michael Jackson', 'genre' => 'Pop',        'release_year' => 1982, 'average_rating' => 4.8],
            ['title' => 'Dark Side of the Moon',  'artist' => 'Pink Floyd',      'genre' => 'Rock',       'release_year' => 1973, 'average_rating' => 4.9],
            ['title' => 'Kind of Blue',            'artist' => 'Miles Davis',     'genre' => 'Jazz',       'release_year' => 1959, 'average_rating' => 4.7],
            ['title' => 'Nevermind',               'artist' => 'Nirvana',         'genre' => 'Rock',       'release_year' => 1991, 'average_rating' => 4.6],
            ['title' => 'Random Access Memories',  'artist' => 'Daft Punk',       'genre' => 'Electrónica','release_year' => 2013, 'average_rating' => 4.5],
            ['title' => 'Back in Black',           'artist' => 'AC/DC',           'genre' => 'Rock',       'release_year' => 1980, 'average_rating' => 4.7],
            ['title' => 'Rumours',                 'artist' => 'Fleetwood Mac',   'genre' => 'Pop',        'release_year' => 1977, 'average_rating' => 4.6],
            ['title' => 'Abbey Road',              'artist' => 'The Beatles',     'genre' => 'Pop',        'release_year' => 1969, 'average_rating' => 4.9],
            ['title' => 'To Pimp a Butterfly',     'artist' => 'Kendrick Lamar',  'genre' => 'Hip-Hop',   'release_year' => 2015, 'average_rating' => 4.8],
            ['title' => 'Homogenic',               'artist' => 'Björk',           'genre' => 'Electrónica','release_year' => 1997, 'average_rating' => 4.4],
        ];

        foreach ($albums as $data) {
            Album::create(array_merge($data, ['user_id' => $user->id]));
        }
    }
}
```

---

## FRAGMENTO D — Migración `add_role_to_users_table` (métodos `up` y `down`)

```php
// Método up()
Schema::table('users', function (Blueprint $table) {
    $table->string('role')->default('user')->after('email');
});
```

```php
// Método down()
Schema::table('users', function (Blueprint $table) {
    $table->dropColumn('role');
});
```

---

## FRAGMENTO E — Métodos a añadir en el modelo `User`

```php
public function isAdmin(): bool
{
    return $this->role === 'admin';
}

public function albums(): HasMany
{
    return $this->hasMany(Album::class);
}
```

> Recuerda añadir al principio del fichero `User.php`:
> ```php
> use Illuminate\Database\Eloquent\Relations\HasMany;
> ```

---

## FRAGMENTO F — Seeder `AdminSeeder`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@musica.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);
    }
}
```

---

## FRAGMENTO G — Middleware `IsAdmin`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
```

---

## FRAGMENTO H — Form Request `StoreAlbumRequest`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:200'],
            'artist'       => ['required', 'string', 'max:200'],
            'genre'        => ['required', 'string', 'max:100'],
            'release_year' => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
            'record_label' => ['nullable', 'string', 'max:150'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'cover'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'El título del álbum es obligatorio.',
            'artist.required'       => 'El nombre del artista es obligatorio.',
            'genre.required'        => 'El género musical es obligatorio.',
            'release_year.required' => 'El año de lanzamiento es obligatorio.',
            'release_year.min'      => 'El año no puede ser anterior a 1900.',
            'cover.image'           => 'La portada debe ser una imagen válida.',
            'cover.max'             => 'La portada no puede superar los 2 MB.',
        ];
    }
}
```

---

## FRAGMENTO I — Controlador `AlbumController`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Http\Requests\StoreAlbumRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    public function index(): View
    {
        $albums = Album::orderByDesc('created_at')->paginate(10);
        return view('albums.index', compact('albums'));
    }

    public function create(): View
    {
        return view('albums.create');
    }

    public function store(StoreAlbumRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        Album::create($data);

        return redirect()->route('albums.index')
            ->with('success', 'Álbum creado correctamente.');
    }

    public function show(Album $album): View
    {
        return view('albums.show', compact('album'));
    }

    public function edit(Album $album): View
    {
        $this->authorizeAccess($album);
        return view('albums.edit', compact('album'));
    }

    public function update(StoreAlbumRequest $request, Album $album): RedirectResponse
    {
        $this->authorizeAccess($album);

        $data = $request->validated();

        if ($request->hasFile('cover')) {
            if ($album->cover) {
                Storage::disk('public')->delete($album->cover);
            }
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $album->update($data);

        return redirect()->route('albums.show', $album)
            ->with('success', 'Álbum actualizado correctamente.');
    }

    public function destroy(Album $album): RedirectResponse
    {
        $this->authorizeAccess($album);

        if ($album->cover) {
            Storage::disk('public')->delete($album->cover);
        }

        $album->delete();

        return redirect()->route('albums.index')
            ->with('success', 'Álbum eliminado.');
    }

    private function authorizeAccess(Album $album): void
    {
        if (auth()->id() !== $album->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }
    }
}
```

---

## FRAGMENTO J — Vista `albums/index.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Álbumes</h1>
        @auth
            <a href="{{ route('albums.create') }}" class="btn btn-primary">+ Añadir álbum</a>
        @endauth
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row row-cols-1 row-cols-md-3 g-4">
        @forelse($albums as $album)
            <div class="col">
                <div class="card h-100">
                    @if($album->cover)
                        <img src="{{ Storage::url($album->cover) }}"
                             class="card-img-top"
                             style="height:200px;object-fit:cover"
                             alt="{{ $album->title }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $album->title }}</h5>
                        <p class="card-text text-muted">{{ $album->artist }} · {{ $album->release_year }}</p>
                        <span class="badge bg-secondary">{{ $album->genre }}</span>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('albums.show', $album) }}" class="btn btn-sm btn-outline-primary">
                            Ver detalle
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">No hay álbumes registrados aún.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $albums->links() }}</div>
</div>
@endsection
```

---

## FRAGMENTO K — Vista `albums/show.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            @if($album->cover)
                <img src="{{ Storage::url($album->cover) }}"
                     class="img-fluid rounded shadow"
                     alt="{{ $album->title }}">
            @else
                <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                     style="height:200px">
                    <span class="text-white fs-1">🎵</span>
                </div>
            @endif
        </div>
        <div class="col-md-9">
            <h1>{{ $album->title }}</h1>
            <p class="text-muted fs-5">{{ $album->artist }}</p>
            <p><strong>Género:</strong> {{ $album->genre }}</p>
            <p><strong>Año:</strong> {{ $album->release_year }}</p>
            @if($album->record_label)
                <p><strong>Discográfica:</strong> {{ $album->record_label }}</p>
            @endif
            @if($album->description)
                <p class="mt-3">{{ $album->description }}</p>
            @endif

            @auth
                @if(auth()->id() === $album->user_id || auth()->user()->isAdmin())
                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('albums.edit', $album) }}" class="btn btn-warning">Editar</a>
                        <form action="{{ route('albums.destroy', $album) }}" method="POST"
                              onsubmit="return confirm('¿Seguro que quieres eliminar este álbum?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</div>
@endsection
```

---

## FRAGMENTO L — Vista `albums/create.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width:700px">
    <h1 class="mb-4">Añadir álbum</h1>

    <form action="{{ route('albums.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Título *</label>
            <input type="text" name="title"
                   class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title') }}">
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Artista *</label>
            <input type="text" name="artist"
                   class="form-control @error('artist') is-invalid @enderror"
                   value="{{ old('artist') }}">
            @error('artist') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Género *</label>
                <input type="text" name="genre"
                       class="form-control @error('genre') is-invalid @enderror"
                       value="{{ old('genre') }}">
                @error('genre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col mb-3">
                <label class="form-label">Año de lanzamiento *</label>
                <input type="number" name="release_year"
                       class="form-control @error('release_year') is-invalid @enderror"
                       value="{{ old('release_year') }}" min="1900" max="{{ date('Y') }}">
                @error('release_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Discográfica</label>
            <input type="text" name="record_label" class="form-control"
                   value="{{ old('record_label') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Portada (imagen)</label>
            <input type="file" name="cover"
                   class="form-control @error('cover') is-invalid @enderror"
                   accept="image/*">
            @error('cover') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar álbum</button>
            <a href="{{ route('albums.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
```

---

## FRAGMENTO M — Vista `albums/edit.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width:700px">
    <h1 class="mb-4">Editar álbum</h1>

    <form action="{{ route('albums.update', $album) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Título *</label>
            <input type="text" name="title"
                   class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title', $album->title) }}">
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Artista *</label>
            <input type="text" name="artist"
                   class="form-control @error('artist') is-invalid @enderror"
                   value="{{ old('artist', $album->artist) }}">
            @error('artist') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Género *</label>
                <input type="text" name="genre"
                       class="form-control @error('genre') is-invalid @enderror"
                       value="{{ old('genre', $album->genre) }}">
                @error('genre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col mb-3">
                <label class="form-label">Año de lanzamiento *</label>
                <input type="number" name="release_year"
                       class="form-control @error('release_year') is-invalid @enderror"
                       value="{{ old('release_year', $album->release_year) }}"
                       min="1900" max="{{ date('Y') }}">
                @error('release_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Discográfica</label>
            <input type="text" name="record_label" class="form-control"
                   value="{{ old('record_label', $album->record_label) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control"
                      rows="4">{{ old('description', $album->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Portada (imagen)</label>
            @if($album->cover)
                <div class="mb-2">
                    <img src="{{ Storage::url($album->cover) }}" height="80" class="rounded">
                    <small class="text-muted ms-2">Portada actual</small>
                </div>
            @endif
            <input type="file" name="cover"
                   class="form-control @error('cover') is-invalid @enderror"
                   accept="image/*">
            @error('cover') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning">Actualizar álbum</button>
            <a href="{{ route('albums.show', $album) }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
```

---

## FRAGMENTO N — Rutas en `web.php`

```php
use App\Http\Controllers\AlbumController;

// Rutas públicas de álbumes
Route::resource('albums', AlbumController::class)->only(['index', 'show']);

// Rutas protegidas: solo usuarios autenticados
Route::middleware('auth')->group(function () {
    Route::resource('albums', AlbumController::class)->except(['index', 'show']);
});
```

---
---

# PARTE 2 — Controlador con errores

> Este controlador contiene **3 errores deliberados**.
> Localiza cada uno, explica qué está mal y escribe la corrección en `errores_resueltos.md`.

```php
<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Método 1
    public function index()
    {
        $albums = Album::all()->paginate(10);
        return view('albums.index', compact('albums'));
    }

    // Método 2
    public function update(Request $request, Album $album)
    {
        if (auth()->id() !== $album->user_id || auth()->user()->isAdmin()) {
            abort(403);
        }

        $album->update($request->validated());

        return redirect()->route('albums.show', $album)
            ->with('success', 'Álbum actualizado.');
    }

    // Método 3
    public function show(Album $album)
    {
        $reviews = $album->reviews()->orderBy('created_at', 'desc')->get();
        return view('albums.show', compact('album'));
    }
}
```
