@extends('layouts.app')

@section('title', $deporte['titulo'] . ' - Portal Deportivo')

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

        .btn-volver {
            display: inline-block;
            margin-top: 30px;
            background: #333;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-volver:hover {
            background: #555;
        }
    </style>

    <div class="detalle-deporte">
        <h1>{{ $deporte['titulo'] }}</h1>

        <div class="info-box">
            <p>{{ $deporte['contenido'] }}</p>

            <div class="datos">
                <div class="dato-item">
                    <strong>Jugadores por equipo:</strong>
                    {{ $deporte['jugadores'] }}
                </div>
                <div class="dato-item">
                    <strong>Origen:</strong>
                    {{ $deporte['origen'] }}
                </div>
            </div>
        </div>

        <a href="{{ route('home') }}" class="btn-volver">← Volver al inicio</a>
    </div>
@endsection
