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
