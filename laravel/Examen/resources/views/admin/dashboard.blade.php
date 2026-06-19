@extends('layouts.app')

@section('title', 'Panel de Administración - MovieHub')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-cog"></i> Panel de Administración</h1>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h3>{{ $stats['total_movies'] }}</h3>
                    <p class="mb-0"><i class="fas fa-film"></i> Total Películas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h3>{{ $stats['total_users'] }}</h3>
                    <p class="mb-0"><i class="fas fa-users"></i> Total Usuarios</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h3>{{ $stats['total_ratings'] }}</h3>
                    <p class="mb-0"><i class="fas fa-star"></i> Total Valoraciones</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h3>{{ number_format($stats['total_ratings'] / max($stats['total_movies'], 1), 1) }}</h3>
                    <p class="mb-0"><i class="fas fa-chart-line"></i> Media Val./Película</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Películas Mejor Valoradas -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-trophy"></i> Top 5 Películas Mejor Valoradas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Título</th>
                            <th>Director</th>
                            <th>Año</th>
                            <th>Valoración</th>
                            <th>Num. Valoraciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['top_movies'] as $index => $movie)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('movies.show', $movie) }}">
                                        {{ $movie->title }}
                                    </a>
                                </td>
                                <td>{{ $movie->director }}</td>
                                <td>{{ $movie->year }}</td>
                                <td>
                                    <span class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($movie->average_rating))
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </span>
                                    <strong>{{ number_format($movie->average_rating, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $movie->ratings->count() }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-users"></i> Gestión de Usuarios</h5>
                </div>
                <div class="card-body text-center">
                    <p>Ver y administrar todos los usuarios de la plataforma</p>
                    <a href="{{ route('admin.users') }}" class="btn btn-primary">
                        <i class="fas fa-users-cog"></i> Gestionar Usuarios
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-film"></i> Gestión de Películas</h5>
                </div>
                <div class="card-body text-center">
                    <p>Ver todas las películas de la plataforma</p>
                    <a href="{{ route('movies.index') }}" class="btn btn-primary">
                        <i class="fas fa-list"></i> Ver Todas las Películas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
