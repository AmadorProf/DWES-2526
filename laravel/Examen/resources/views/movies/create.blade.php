@extends('layouts.app')

@section('title', 'Añadir Nueva Película - MovieHub')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-plus-circle"></i> Añadir Nueva Película</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('movies.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Título -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Título *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Director -->
                        <div class="mb-3">
                            <label for="director" class="form-label">Director *</label>
                            <input type="text" class="form-control @error('director') is-invalid @enderror" 
                                   id="director" name="director" value="{{ old('director') }}" required>
                            @error('director')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Año -->
                            <div class="col-md-4 mb-3">
                                <label for="year" class="form-label">Año *</label>
                                <input type="number" class="form-control @error('year') is-invalid @enderror" 
                                       id="year" name="year" value="{{ old('year') }}" 
                                       min="1900" max="{{ date('Y') }}" required>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Duración -->
                            <div class="col-md-4 mb-3">
                                <label for="duration" class="form-label">Duración (min) *</label>
                                <input type="number" class="form-control @error('duration') is-invalid @enderror" 
                                       id="duration" name="duration" value="{{ old('duration') }}" 
                                       min="1" required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Género -->
                            <div class="col-md-4 mb-3">
                                <label for="genre" class="form-label">Género *</label>
                                <select class="form-select @error('genre') is-invalid @enderror" 
                                        id="genre" name="genre" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Acción" {{ old('genre') == 'Acción' ? 'selected' : '' }}>Acción</option>
                                    <option value="Aventura" {{ old('genre') == 'Aventura' ? 'selected' : '' }}>Aventura</option>
                                    <option value="Ciencia Ficción" {{ old('genre') == 'Ciencia Ficción' ? 'selected' : '' }}>Ciencia Ficción</option>
                                    <option value="Comedia" {{ old('genre') == 'Comedia' ? 'selected' : '' }}>Comedia</option>
                                    <option value="Drama" {{ old('genre') == 'Drama' ? 'selected' : '' }}>Drama</option>
                                    <option value="Terror" {{ old('genre') == 'Terror' ? 'selected' : '' }}>Terror</option>
                                    <option value="Thriller" {{ old('genre') == 'Thriller' ? 'selected' : '' }}>Thriller</option>
                                    <option value="Romance" {{ old('genre') == 'Romance' ? 'selected' : '' }}>Romance</option>
                                    <option value="Animación" {{ old('genre') == 'Animación' ? 'selected' : '' }}>Animación</option>
                                </select>
                                @error('genre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Sinopsis -->
                        <div class="mb-3">
                            <label for="synopsis" class="form-label">Sinopsis *</label>
                            <textarea class="form-control @error('synopsis') is-invalid @enderror" 
                                      id="synopsis" name="synopsis" rows="4" required>{{ old('synopsis') }}</textarea>
                            @error('synopsis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reparto -->
                        <div class="mb-3">
                            <label for="cast" class="form-label">Reparto Principal</label>
                            <input type="text" class="form-control @error('cast') is-invalid @enderror" 
                                   id="cast" name="cast" value="{{ old('cast') }}" 
                                   placeholder="Ej: Actor 1, Actor 2, Actor 3">
                            @error('cast')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- País -->
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">País</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                       id="country" name="country" value="{{ old('country') }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Clasificación por edad -->
                            <div class="col-md-6 mb-3">
                                <label for="age_rating" class="form-label">Clasificación</label>
                                <select class="form-select @error('age_rating') is-invalid @enderror" 
                                        id="age_rating" name="age_rating">
                                    <option value="">Seleccionar...</option>
                                    <option value="ATP" {{ old('age_rating') == 'ATP' ? 'selected' : '' }}>ATP</option>
                                    <option value="+13" {{ old('age_rating') == '+13' ? 'selected' : '' }}>+13</option>
                                    <option value="+16" {{ old('age_rating') == '+16' ? 'selected' : '' }}>+16</option>
                                    <option value="+18" {{ old('age_rating') == '+18' ? 'selected' : '' }}>+18</option>
                                </select>
                                @error('age_rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Póster -->
                        <div class="mb-3">
                            <label for="poster" class="form-label">Póster de la Película</label>
                            <input type="file" class="form-control @error('poster') is-invalid @enderror" 
                                   id="poster" name="poster" accept="image/*">
                            <small class="form-text text-muted">
                                Formatos: JPG, PNG, GIF. Tamaño máximo: 2MB
                            </small>
                            @error('poster')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('movies.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
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
