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
