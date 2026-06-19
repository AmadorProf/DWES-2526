@extends('layouts.app')

@section('title', 'Inicio - Portal Deportivo')

@section('content')
    <style>
        .deportes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            transition: transform 0.3s;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        .intro {
            text-align: center;
            margin-bottom: 40px;
        }

        .intro h1 {
            color: #333;
            margin-bottom: 15px;
        }

        .intro p {
            color: #666;
            font-size: 1.1rem;
        }
    </style>

    <div class="intro">
        <h1>Bienvenido al Portal Deportivo</h1>
        <p>Descubre información sobre los deportes más populares del mundo</p>
    </div>

    <div class="deportes-grid">
        @foreach($deportes as $deporte)
            <div class="deporte-card">
                <h2>{{ $deporte['nombre'] }}</h2>
                <p>{{ $deporte['descripcion'] }}</p>
                <a href="{{ route($deporte['ruta']) }}" class="btn">Ver más</a>
            </div>
        @endforeach
    </div>
@endsection
