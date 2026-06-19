@extends('layouts.app')

@section('title', 'Todas las Películas - MovieHub')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-film"></i> Catálogo de Películas</h1>
        @auth
            <a href="{{ route('movies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Añadir Película
            </a>
        @endauth
    </div>

    @if($movies->count() > 0)
        <div class="row">
            @foreach($movies as $movie)
                <div class="col-md-4 mb-4">
                    <div class="card movie-card h-100">
                        <img src="{{ asset('storage/' . $movie->poster) }}" 
                             class="card-img-top card-poster" 
                             alt="{{ $movie->title }}"
                             onerror="this.src='https://via.placeholder.com/300x400?text=Sin+Imagen'">
                        <div class="card-body">
                            <h5 class="card-title">{{ $movie->title }}</h5>
                            <p class="card-text text-muted mb-2">
                                <small>
                                    <i class="fas fa-user-tie"></i> {{ $movie->director }} |
                                    <i class="fas fa-calendar"></i> {{ $movie->year }}
                                </small>
                            </p>
                            <p class="card-text">
                                <span class="badge bg-secondary">{{ $movie->genre }}</span>
                                <span class="badge bg-info">{{ $movie->duration }} min</span>
                            </p>
                            
                            <!-- Valoración -->
                            <div class="mb-2">
                                <span class="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($movie->average_rating))
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </span>
                                <small class="text-muted">
                                    ({{ number_format($movie->average_rating, 1) }}/5)
                                </small>
                            </div>

                            <p class="card-text">
                                <small class="text-muted">
                                    Por: {{ $movie->user->name }}
                                </small>
                            </p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('movies.show', $movie) }}" class="btn btn-primary btn-sm w-100">
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
            <i class="fas fa-info-circle"></i> 
            No hay películas registradas todavía.
            @auth
                <a href="{{ route('movies.create') }}" class="alert-link">¡Sé el primero en añadir una!</a>
            @endauth
        </div>
    @endif
</div>
@endsection
