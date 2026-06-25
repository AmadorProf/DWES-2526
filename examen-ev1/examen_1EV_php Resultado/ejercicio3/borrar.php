
<?php
require_once 'conexion.php';

$mensaje = "";
$error = "";

// Verificar que se ha recibido un ID
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Obtener el nombre del hechizo antes de eliminarlo
    $stmt = $conexion->prepare("SELECT nombre FROM hechizos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $hechizo = $resultado->fetch_assoc();
        $nombre_hechizo = $hechizo['nombre'];
        
        // Eliminar el hechizo
        $stmt_delete = $conexion->prepare("DELETE FROM hechizos WHERE id = ?");
        $stmt_delete->bind_param("i", $id);
        
        if ($stmt_delete->execute()) {
            $mensaje = "El hechizo '{$nombre_hechizo}' ha sido borrado de la biblioteca mágica.";
        } else {
            $error = "Error al eliminar el hechizo: " . $stmt_delete->error;
        }
        
        $stmt_delete->close();
    } else {
        $error = "El hechizo con ID {$id} no existe en la biblioteca.";
    }
    
    $stmt->close();
} else {
    $error = "No se ha especificado ningún hechizo para eliminar.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrar Hechizo</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <header>
        <h1>EXAMEN 1EV: Ejercicio 3</h1>
    </header>
    
    <main>
        <h2>Eliminar Hechizo</h2>
        
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
        
        <div class="button-row">
            <a href="listar.php" class="btn">Volver al Catálogo Místico</a>
            <a href="agregar.php" class="btn btn-success">Añadir Nuevo Hechizo</a>
        </div>
    </main>
</body>
</html>
