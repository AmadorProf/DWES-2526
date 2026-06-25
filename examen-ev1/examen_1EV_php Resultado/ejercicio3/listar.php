
<?php
require_once 'conexion.php';

// Consultar todos los hechizos
$query = "SELECT id, nombre, tipo, nivel_poder FROM hechizos ORDER BY nivel_poder DESC";
$resultado = $conexion->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Hechizos</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <header>
        <h1>EXAMEN 1EV: Ejercicio 3</h1>
    </header>
    
    <main>
        <h2>Catálogo Místico de Hechizos </h2>
        
        <div class="button-row">
            <a href="agregar.php" class="btn btn-success">Añadir Nuevo Hechizo</a>
        </div>
        
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Nivel de Poder</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($hechizo = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($hechizo['id']); ?></td>
                            <td><?php echo htmlspecialchars($hechizo['nombre']); ?></td>
                            <td>
                                <?php
                                $icono = '';
                                switch ($hechizo['tipo']) {
                                    case 'ataque':
                                        $icono = 'Ataque';
                                        break;
                                    case 'defensa':
                                        $icono = 'Defensa';
                                        break;
                                    case 'curación':
                                        $icono = 'Curar';
                                        break;
                                }
                                echo $icono . ' ' . ucfirst($hechizo['tipo']);
                                ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($hechizo['nivel_poder']); ?></strong>/100
                            </td>
                            <td>
                                <a href="borrar.php?id=<?php echo $hechizo['id']; ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('¿Estás seguro de que deseas eliminar este hechizo de la biblioteca?');">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="panel">
                <p>No hay hechizos en la biblioteca mágica.</p>
                <p>¡Comienza añadiendo tu primer hechizo!</p>
            </div>
        <?php endif; ?>
        
        <div class="panel">
            <p><strong>Total de hechizos en la biblioteca:</strong> <?php echo $resultado ? $resultado->num_rows : 0; ?></p>
        </div>
    </main>
</body>
</html>
