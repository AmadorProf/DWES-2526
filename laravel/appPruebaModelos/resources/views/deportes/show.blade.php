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
