<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Deportivo')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        nav {
            background: #333;
            padding: 15px;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        nav a:hover {
            background: #555;
        }

        .content {
            padding: 40px;
        }

        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>🏆 Portal Deportivo</h1>
        <p>Tu fuente de información deportiva</p>
    </header>

    <nav>
        <a href="{{ route('home') }}">Inicio</a>
        <a href="{{ route('deportes.futbol') }}">Fútbol</a>
        <a href="{{ route('deportes.baloncesto') }}">Baloncesto</a>
        <a href="{{ route('deportes.tenis') }}">Tenis</a>
    </nav>

    <div class="content">
        @yield('content')
    </div>

    <footer>
        <p>&copy; 2025 Portal Deportivo - Hecho con Laravel 12</p>
    </footer>
</div>
</body>
</html>
