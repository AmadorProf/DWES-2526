<?php
// Inicializar variables desde POST o valores por defecto
$energia = isset($_POST['energia']) ? (int)$_POST['energia'] : 100;
$oro = isset($_POST['oro']) ? (int)$_POST['oro'] : 0;
$historial = isset($_POST['historial']) ? json_decode($_POST['historial'], true) : [];
$mensaje = "";
$accion_realizada = false;

// Procesar acción si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    $accion_realizada = true;
    
    switch ($accion) {
        case 'explorar':
            $energia -= 10;
            $oro_ganado = rand(10, 50);
            $oro += $oro_ganado;
            $mensaje = "Has explorado el bosque oscuro. Perdiste 10 de energía pero encontraste {$oro_ganado} monedas de oro brillando entre las hojas.";
            $historial[] = "Explorar el bosque (+{$oro_ganado} oro, -10 energía)";
            break;
            
        case 'luchar':
            $energia_perdida = rand(10, 30);
            $energia -= $energia_perdida;
            $gano_batalla = (rand(0, 1) === 1);
            if ($gano_batalla) {
                $oro += 40;
                $mensaje = "¡Has luchado valientemente contra un monstruo! Perdiste {$energia_perdida} de energía, pero ganaste 40 monedas de oro del botín.";
                $historial[] = "Luchar (+40 oro, -{$energia_perdida} energía) ¡Victoria!";
            } else {
                $mensaje = "Has luchado contra un monstruo y lo derrotaste, pero no llevaba oro. Perdiste {$energia_perdida} de energía.";
                $historial[] = "Luchar (sin oro, -{$energia_perdida} energía)";
            }
            break;
            
        case 'descansar':
            $energia += 20;
            if ($energia > 100) {
                $energia = 100;
            }
            $mensaje = "Te has sentado junto al fuego a descansar. Recuperaste 20 puntos de energía y te sientes renovado.";
            $historial[] = "Descansar (+20 energía)";
            break;
    }
    
    // Mantener solo las últimas 5 acciones
    if (count($historial) > 5) {
        $historial = array_slice($historial, -5);
    }
}

// Verificar si ha ganado
$ha_ganado = ($oro >= 100);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aventura Interactiva</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <header>
        <h1>EXAMEN 1EV: Ejercicio 1</h1>
    </header>
    
    <main>
        <h2>La Aventura del Examen de PHP</h2>
        
        <!-- Panel de Estado -->
        <div class="panel panel-estado">
            <h3>Estado del Aventurero</h3>
            <p><strong>Energía:</strong> <?php echo $energia; ?>/100</p>
            <p><strong>Oro:</strong> <?php echo $oro; ?>/100</p>
        </div>
        
        <?php if ($ha_ganado): ?>
            <!-- Mensaje de victoria -->
            <div class="mensaje mensaje-victoria">
                ¡FELICIDADES! <br>
                Has conseguido acumular 100 monedas de oro.<br>
                ¡Has ganado la aventura!
            </div>
        <?php else: ?>
            <!-- Mensaje de la última acción -->
            <?php if ($mensaje): ?>
                <div class="mensaje mensaje-info">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulario de acciones -->
            <form method="POST" action="aventura.php">
                <input type="hidden" name="energia" value="<?php echo $energia; ?>">
                <input type="hidden" name="oro" value="<?php echo $oro; ?>">
                <input type="hidden" name="historial" value='<?php echo htmlspecialchars(json_encode($historial)); ?>'>
                
                <h3>¿Qué deseas hacer?</h3>
                
                <div class="button-row">
                    <button type="submit" name="accion" value="explorar" <?php echo ($energia < 10) ? 'disabled' : ''; ?>>
                        Explorar el Bosque
                    </button>
                    <button type="submit" name="accion" value="luchar" <?php echo ($energia < 10) ? 'disabled' : ''; ?>>
                        Luchar contra Monstruo
                    </button>
                    <button type="submit" name="accion" value="descansar" <?php echo ($energia >= 100) ? 'disabled' : ''; ?>>
                        Descansar
                    </button>
                </div>
                
                <?php if ($energia < 10): ?>
                    <p style="color: #e74c3c; font-weight: bold;">No tienes suficiente energía. Debes descansar.</p>
                <?php endif; ?>
            </form>
        <?php endif; ?>
        
        <!-- Historial de acciones -->
        <?php if (!empty($historial)): ?>
            <div class="panel panel-historial">
                <h3>Historial de Acciones</h3>
                <ul class="historial-lista">
                    <?php foreach (array_reverse($historial) as $accion): ?>
                        <li><?php echo htmlspecialchars($accion); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
