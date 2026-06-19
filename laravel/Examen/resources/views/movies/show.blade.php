@extends('layouts.app')

@section('title', $movie->title . ' - MovieHub')

@section('content')
<div class="container">
    <!-- Botón volver -->
    <a href="{{ route('movies.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al catálogo
    </a>

    <div class="row">
        <!-- Imagen -->
        <div class="col-md-4">
            <img src="{{ asset('storage/' . $movie->poster) }}" 
                 class="img-fluid rounded shadow poster-img w-100" 
                 alt="{{ $movie->title }}"
                 onerror="this.src='https://via.placeholder.com/400x600?text=Sin+Imagen'">
            
            <!-- Botones de acción (solo para creador o admin) -->
            @auth
                @if(auth()->id() === $movie->user_id || auth()->user()->isAdmin())
                    <div class="mt-3 d-grid gap-2">
                        <a href="{{ route('movies.edit', $movie) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="{{ route('movies.destroy', $movie) }}" method="POST" 
                              onsubmit="return confirm('¿Estás seguro de eliminar esta película?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Información -->
        <div class="col-md-8">
            <h1>{{ $movie->title }}</h1>
            
            <!-- Valoración -->
            <div class="mb-3">
                <span class="star-rating fs-4">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($movie->average_rating))
                            <i class="fas fa-star"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                </span>
                <span class="fs-5 ms-2">
                    {{ number_format($movie->average_rating, 1) }}/5
                </span>
                <small class="text-muted">
                    ({{ $movie->ratings->count() }} valoraciones)
                </small>
            </div>

            <!-- Información básica -->
            <div class="card mb-3">
                <div class="card-body">
                    <p><strong><i class="fas fa-user-tie"></i> Director:</strong> {{ $movie->director }}</p>
                    <p><strong><i class="fas fa-calendar"></i> Año:</strong> {{ $movie->year }}</p>
                    <p><strong><i class="fas fa-tag"></i> Género:</strong> 
                        <span class="badge bg-secondary">{{ $movie->genre }}</span>
                    </p>
                    <p><strong><i class="fas fa-clock"></i> Duración:</strong> {{ $movie->duration }} minutos</p>
                    @if($movie->country)
                        <p><strong><i class="fas fa-globe"></i> País:</strong> {{ $movie->country }}</p>
                    @endif
                    @if($movie->age_rating)
                        <p><strong><i class="fas fa-exclamation-triangle"></i> Clasificación:</strong> 
                            <span class="badge bg-warning text-dark">{{ $movie->age_rating }}</span>
                        </p>
                    @endif
                    @if($movie->cast)
                        <p><strong><i class="fas fa-users"></i> Reparto:</strong> {{ $movie->cast }}</p>
                    @endif
                    <p><strong><i class="fas fa-user"></i> Añadida por:</strong> {{ $movie->user->name }}</p>
                </div>
            </div>

            <!-- Sinopsis -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5><i class="fas fa-align-left"></i> Sinopsis</h5>
                </div>
                <div class="card-body">
                    <p>{{ $movie->synopsis }}</p>
                </div>
            </div>

            <!-- Sistema de valoración -->
            @auth
                <div class="card mb-3">
                    <div class="card-header">
                        <h5><i class="fas fa-star"></i> Tu Valoración</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('ratings.store', $movie) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Puntúa esta película:</label>
                                <div class="btn-group" role="group">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" class="btn-check" name="score" 
                                               id="score{{ $i }}" value="{{ $i }}"
                                               {{ $userRating && $userRating->score == $i ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning" for="score{{ $i }}">
                                            {{ $i }} <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                {{ $userRating ? 'Actualizar Valoración' : 'Valorar' }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <a href="{{ route('login') }}">Inicia sesión</a> para valorar esta película.
                </div>
            @endauth
        </div>
    </div>

    <!-- Sección de Reseñas -->
    <div class="row mt-5">
        <div class="col-12">
            <h3><i class="fas fa-comments"></i> Reseñas</h3>
            <hr>

            <!-- Formulario para nueva reseña (solo usuarios autenticados) -->
            @auth
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Escribe tu reseña</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('reviews.store', $movie) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">Título (opcional)</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="content" class="form-label">Reseña</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" name="content" rows="4" 
                                          required>{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Publicar Reseña
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <a href="{{ route('login') }}">Inicia sesión</a> para escribir una reseña.
                </div>
            @endauth

            <!-- Lista de reseñas -->
            @if($movie->reviews->count() > 0)
                @foreach($movie->reviews as $review)
                    <div class="card mb-3">
                        <div class="card-body">
                            @if($review->title)
                                <h5 class="card-title">{{ $review->title }}</h5>
                            @endif
                            <p class="card-text">{{ $review->content }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> {{ $review->user->name }} - 
                                    <i class="fas fa-calendar"></i> {{ $review->created_at->format('d/m/Y') }}
                                </small>
                                
                                @auth
                                    @if(auth()->id() === $review->user_id || auth()->user()->isAdmin())
                                        <form action="{{ route('reviews.destroy', $review) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar esta reseña?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-secondary">
                    <i class="fas fa-info-circle"></i>
                    Aún no hay reseñas para esta película. ¡Sé el primero en escribir una!
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
