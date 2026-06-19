@extends('layouts.app')

@section('title', 'Mi Perfil - MovieHub')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-user-circle fa-5x text-primary mb-3"></i>
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    <span class="badge bg-{{ $user->isAdmin() ? 'danger' : 'primary' }}">
                        {{ $user->isAdmin() ? 'Administrador' : 'Usuario' }}
                    </span>
                    <hr>
                    <div class="row text-center">
                        <div class="col-4">
                            <h5>{{ $user->movies->count() }}</h5>
                            <small class="text-muted">Películas</small>
                        </div>
                        <div class="col-4">
                            <h5>{{ $user->ratings->count() }}</h5>
                            <small class="text-muted">Valoraciones</small>
                        </div>
                        <div class="col-4">
                            <h5>{{ $user->reviews->count() }}</h5>
                            <small class="text-muted">Reseñas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-film"></i> Mis Películas</h5>
                </div>
                <div class="card-body">
                    @if($movies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Año</th>
                                        <th>Género</th>
                                        <th>Valoración</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movies as $movie)
                                        <tr>
                                            <td>{{ $movie->title }}</td>
                                            <td>{{ $movie->year }}</td>
                                            <td><span class="badge bg-secondary">{{ $movie->genre }}</span></td>
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
                                                <small>({{ number_format($movie->average_rating, 1) }})</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('movies.show', $movie) }}" 
                                                   class="btn btn-sm btn-info" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('movies.edit', $movie) }}" 
                                                   class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $movies->links() }}
                        </div>
                    @else
                        <p class="text-muted text-center">
                            Aún no has añadido ninguna película.
                            <a href="{{ route('movies.create') }}">¡Añade la primera!</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
