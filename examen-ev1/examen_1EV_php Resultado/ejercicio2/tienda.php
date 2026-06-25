<?php
session_start();

// Control de acceso
if (!isset($_SESSION['usuario'])) {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acceso Denegado</title>
        <link rel="stylesheet" href="../estilos.css">
        <meta http-equiv="refresh" content="3;url=login.php">
    </head>
    <body>
        <header>
            <h1>EXAMEN 1EV: Ejercicio 2</h1>
        </header>
        <main>
            <div class="mensaje mensaje-error">
                Debes iniciar sesión para acceder a la tienda.<br>
                Redirigiendo en 3 segundos...
            </div>
        </main>
    </body>
    </html>';
    exit();
}

// Catálogo de videojuegos
$videojuegos = [
    ['id' => 1, 'nombre' => 'Super Mario World', 'precio' => 29.99, 'genero' => 'acción', 'stock' => 10],
    ['id' => 2, 'nombre' => 'The Legend of Zelda: ALTTP', 'precio' => 34.99, 'genero' => 'aventura', 'stock' => 8],
    ['id' => 3, 'nombre' => 'Final Fantasy VI', 'precio' => 39.99, 'genero' => 'RPG', 'stock' => 5],
    ['id' => 4, 'nombre' => 'Civilization II', 'precio' => 24.99, 'genero' => 'estrategia', 'stock' => 12],
    ['id' => 5, 'nombre' => 'Street Fighter II', 'precio' => 27.99, 'genero' => 'acción', 'stock' => 15],
    ['id' => 6, 'nombre' => 'Chrono Trigger', 'precio' => 44.99, 'genero' => 'RPG', 'stock' => 6]
];

$mensaje = "";

// Procesar añadir a reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['juegos'])) {
    $juegos_seleccionados = $_POST['juegos'];
    $cantidades = $_POST['cantidad'] ?? [];
    
    $error_validacion = false;
    $reservas = [];
    
    // Leer cookie de reservas existente
    if (isset($_COOKIE['reservas'])) {
        $reservas = unserialize($_COOKIE['reservas']);
    }
    
    $juegos_anadidos = 0;
    
    foreach ($juegos_seleccionados as $id_juego) {
        $cantidad = (int)($cantidades[$id_juego] ?? 0);
        
        if ($cantidad <= 0) {
            $error_validacion = true;
            $mensaje = "La cantidad debe ser mayor a 0.";
            break;
        }
        
        // Buscar el juego en el catálogo
        $juego = null;
        foreach ($videojuegos as $v) {
            if ($v['id'] == $id_juego) {
                $juego = $v;
                break;
            }
        }
        
        if ($juego && $cantidad <= $juego['stock']) {
            $subtotal = $juego['precio'] * $cantidad;
            
            // Verificar si ya existe en reservas
            $existe = false;
            foreach ($reservas as &$reserva) {
                if ($reserva['id'] == $id_juego) {
                    $nueva_cantidad = $reserva['cantidad'] + $cantidad;
                    if ($nueva_cantidad <= $juego['stock']) {
                        $reserva['cantidad'] = $nueva_cantidad;
                        $reserva['subtotal'] = $juego['precio'] * $nueva_cantidad;
                        $existe = true;
                        $juegos_anadidos++;
                    } else {
                        $error_validacion = true;
                        $mensaje = "No hay suficiente stock de {$juego['nombre']}.";
                    }
                    break;
                }
            }
            
            if (!$existe && !$error_validacion) {
                $reservas[] = [
                    'id' => $juego['id'],
                    'nombre' => $juego['nombre'],
                    'precio' => $juego['precio'],
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal,
                    'genero' => $juego['genero']
                ];
                $juegos_anadidos++;
            }
        } else {
            $error_validacion = true;
            $mensaje = "Stock insuficiente para el juego seleccionado.";
            break;
        }
    }
    
    if (!$error_validacion && $juegos_anadidos > 0) {
        setcookie('reservas', serialize($reservas), time() + 7200, '/');
        $mensaje = "Se añadieron {$juegos_anadidos} juego(s) a tu reserva.";
    }
}

// Vaciar reserva
if (isset($_GET['vaciar'])) {
    setcookie('reservas', '', time() - 3600, '/');
    header('Location: tienda.php');
    exit();
}

// Leer reservas actuales
$reservas_actuales = [];
$total_reservas = 0;
$precio_total = 0;

if (isset($_COOKIE['reservas'])) {
    $reservas_actuales = unserialize($_COOKIE['reservas']);
    $total_reservas = count($reservas_actuales);
    foreach ($reservas_actuales as $reserva) {
        $precio_total += $reserva['subtotal'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda - Pixel Paradise</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <header>
        <h1>EXAMEN 1EV: Ejercicio 2</h1>
    </header>
    
    <main>
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_completo']); ?>! </h2>
        
        <div class="button-row">
            <a href="?logout=1" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        
        <?php
        // Procesar logout
        if (isset($_GET['logout'])) {
            session_destroy();
            header('Location: login.php');
            exit();
        }
        ?>
        
        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo (strpos($mensaje, 'añadieron') !== false) ? 'mensaje-exito' : 'mensaje-error'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <h3>Catálogo de Videojuegos Retro</h3>
        
        <form method="POST" action="tienda.php">
            <div class="checkbox-group">
                <?php foreach ($videojuegos as $juego): ?>
                    <?php
                    $es_recomendado = ($juego['genero'] === $_SESSION['genero_preferido']);
                    ?>
                    <div class="checkbox-item juego-card <?php echo $es_recomendado ? 'juego-recomendado' : ''; ?>">
                        <input type="checkbox" name="juegos[]" value="<?php echo $juego['id']; ?>" id="juego_<?php echo $juego['id']; ?>">
                        <label for="juego_<?php echo $juego['id']; ?>" style="flex: 1; text-align: left;">
                            <strong><?php echo htmlspecialchars($juego['nombre']); ?></strong><br>
                            <small>Género: <?php echo ucfirst($juego['genero']); ?> | Precio: €<?php echo number_format($juego['precio'], 2); ?> | Stock: <?php echo $juego['stock']; ?></small>
                            <?php if ($es_recomendado): ?>
                                <span class="etiqueta-recomendado"> Recomendado para ti</span>
                            <?php endif; ?>
                        </label>
                        <input type="number" name="cantidad[<?php echo $juego['id']; ?>]" min="1" max="<?php echo $juego['stock']; ?>" value="1" style="width: 60px;">
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="submit">Añadir a Reserva</button>
        </form>
        
        <!-- Panel de reservas -->
        <div class="panel panel-reservas">
            <h3>Resumen de Reservas</h3>
            <?php if ($total_reservas > 0): ?>
                <p><strong>Juegos reservados:</strong> <?php echo $total_reservas; ?></p>
                <p><strong>Precio total:</strong> €<?php echo number_format($precio_total, 2); ?></p>
                
                <table>
                    <thead>
                        <tr>
                            <th>Juego</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas_actuales as $reserva): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reserva['nombre']); ?></td>
                                <td><?php echo $reserva['cantidad']; ?></td>
                                <td>€<?php echo number_format($reserva['subtotal'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <a href="?vaciar=1" class="btn btn-danger" style="margin-top: 10px;">Vaciar Reserva</a>
            <?php else: ?>
                <p>No tienes juegos reservados.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
