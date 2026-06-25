<?php
require_once 'conexion.php';

$mensaje = "";
$error = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $nivel_poder = (int)($_POST['nivel_poder'] ?? 0);
    
    // Validaciones
    if (empty($nombre)) {
        $error = "El nombre del hechizo es obligatorio.";
    } elseif (!in_array($tipo, ['ataque', 'defensa', 'curación'])) {
        $error = "El tipo de hechizo no es válido.";
    } elseif ($nivel_poder < 1 || $nivel_poder > 100) {
        $error = "El nivel de poder debe estar entre 1 y 100.";
    } else {
        // Preparar consulta
        $stmt = $conexion->prepare("INSERT INTO hechizos (nombre, tipo, nivel_poder) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $nombre, $tipo, $nivel_poder);
        
        if ($stmt->execute()) {
            $mensaje = "¡El hechizo '{$nombre}' ha sido añadido a la biblioteca mágica con éxito! ";
            // Limpiar el formulario
            $nombre = "";
            $tipo = "";
            $nivel_poder = "";
        } else {
            $error = "Error al agregar el hechizo: " . $stmt->error;
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Hechizo</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <header>
        <h1>EXAMEN 1EV: Ejercicio 3</h1>
    </header>
    
    <main>
        <h2>Añadir Nuevo Hechizo </h2>
        
        <?php if ($mensaje): ?>
            <div class="mensaje mensaje-exito">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="mensaje mensaje-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="agregar.php">
            <label for="nombre">Nombre del Hechizo:</label>
            <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($nombre ?? ''); ?>">
            
            <label for="tipo">Tipo de Hechizo:</label>
            <select id="tipo" name="tipo" required>
                <option value="">Selecciona un tipo</option>
                <option value="ataque" <?php echo (isset($tipo) && $tipo === 'ataque') ? 'selected' : ''; ?>>Ataque</option>
                <option value="defensa" <?php echo (isset($tipo) && $tipo === 'defensa') ? 'selected' : ''; ?>>Defensa</option>
                <option value="curación" <?php echo (isset($tipo) && $tipo === 'curación') ? 'selected' : ''; ?>>Curación</option>
            </select>
            
            <label for="nivel_poder">Nivel de Poder (1-100):</label>
            <input type="number" id="nivel_poder" name="nivel_poder" min="1" max="100" required value="<?php echo htmlspecialchars($nivel_poder ?? ''); ?>">
            
            <div class="button-row">
                <button type="submit">Añadir Hechizo a la Biblioteca</button>
                <a href="listar.php" class="btn">Ver Catálogo Místico</a>
            </div>
        </form>
    </main>
</body>
</html>
