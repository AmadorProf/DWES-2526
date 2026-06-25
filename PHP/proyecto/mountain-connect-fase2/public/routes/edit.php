<?php
/**
 * Editar ruta existente
 */
require_once __DIR__ . '/../../includes/header.php';

// Verificar autenticación
requireAuth();

$routeId = $_GET['id'] ?? '';
if (empty($routeId)) {
    header('Location: list.php');
    exit();
}

// Buscar la ruta
$routeIndex = null;
$route = null;
foreach ($_SESSION['routes'] as $index => $r) {
    if ($r['id'] === $routeId) {
        $route = $r;
        $routeIndex = $index;
        break;
    }
}

if (!$route) {
    header('Location: list.php');
    exit();
}

// Verificar que el usuario sea el propietario
$user = getCurrentUser();
if ($route['user_id'] !== $user['id']) {
    header('Location: view.php?id=' . $routeId);
    exit();
}

$errors = [];
$success = false;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizeInput($_POST['nombre'] ?? '');
    $dificultad = sanitizeInput($_POST['dificultad'] ?? '');
    $distancia = sanitizeInput($_POST['distancia'] ?? '');
    $desnivel = sanitizeInput($_POST['desnivel'] ?? '');
    $duracion = sanitizeInput($_POST['duracion'] ?? '');
    $provincia = sanitizeInput($_POST['provincia'] ?? '');
    $epocas = $_POST['epocas'] ?? [];
    $descripcion = sanitizeInput($_POST['descripcion'] ?? '');
    $nivel_tecnico = sanitizeInput($_POST['nivel_tecnico'] ?? '');
    $nivel_fisico = sanitizeInput($_POST['nivel_fisico'] ?? '');
    
    // Validaciones (mismas que en create.php)
    if (empty($nombre)) $errors[] = "El nombre es obligatorio";
    if (empty($dificultad)) $errors[] = "La dificultad es obligatoria";
    if (empty($distancia) || !is_numeric($distancia) || $distancia <= 0) {
        $errors[] = "La distancia debe ser un número mayor que 0";
    }
    if (empty($desnivel) || !is_numeric($desnivel) || $desnivel < 0) {
        $errors[] = "El desnivel debe ser un número positivo";
    }
    if (empty($duracion) || !is_numeric($duracion) || $duracion <= 0) {
        $errors[] = "La duración debe ser un número mayor que 0";
    }
    if (empty($provincia)) $errors[] = "La provincia es obligatoria";
    if (empty($descripcion) || strlen($descripcion) < 20) {
        $errors[] = "La descripción debe tener al menos 20 caracteres";
    }
    
    // Si no hay errores, actualizar la ruta
    if (empty($errors)) {
        $_SESSION['routes'][$routeIndex]['nombre'] = $nombre;
        $_SESSION['routes'][$routeIndex]['dificultad'] = $dificultad;
        $_SESSION['routes'][$routeIndex]['distancia'] = $distancia;
        $_SESSION['routes'][$routeIndex]['desnivel'] = $desnivel;
        $_SESSION['routes'][$routeIndex]['duracion'] = $duracion;
        $_SESSION['routes'][$routeIndex]['provincia'] = $provincia;
        $_SESSION['routes'][$routeIndex]['epocas'] = $epocas;
        $_SESSION['routes'][$routeIndex]['descripcion'] = $descripcion;
        $_SESSION['routes'][$routeIndex]['nivel_tecnico'] = $nivel_tecnico;
        $_SESSION['routes'][$routeIndex]['nivel_fisico'] = $nivel_fisico;
        
        $route = $_SESSION['routes'][$routeIndex];
        $success = true;
    } else {
        // Actualizar $route con los datos del formulario para mantenerlos
        $route['nombre'] = $nombre;
        $route['dificultad'] = $dificultad;
        $route['distancia'] = $distancia;
        $route['desnivel'] = $desnivel;
        $route['duracion'] = $duracion;
        $route['provincia'] = $provincia;
        $route['epocas'] = $epocas;
        $route['descripcion'] = $descripcion;
        $route['nivel_tecnico'] = $nivel_tecnico;
        $route['nivel_fisico'] = $nivel_fisico;
    }
}
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-warning">
                <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Ruta</h4>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        ¡Ruta actualizada exitosamente!
                        <a href="view.php?id=<?php echo $route['id']; ?>">Ver ruta</a>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <strong><i class="fas fa-exclamation-triangle"></i> Errores:</strong>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre de la ruta *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                       value="<?php echo htmlspecialchars($route['nombre']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="dificultad" class="form-label">Dificultad *</label>
                                <select class="form-select" id="dificultad" name="dificultad" required>
                                    <option value="facil" <?php echo $route['dificultad'] === 'facil' ? 'selected' : ''; ?>>Fácil</option>
                                    <option value="moderada" <?php echo $route['dificultad'] === 'moderada' ? 'selected' : ''; ?>>Moderada</option>
                                    <option value="dificil" <?php echo $route['dificultad'] === 'dificil' ? 'selected' : ''; ?>>Difícil</option>
                                    <option value="muy_dificil" <?php echo $route['dificultad'] === 'muy_dificil' ? 'selected' : ''; ?>>Muy Difícil</option>
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="distancia" class="form-label">Distancia (km) *</label>
                                        <input type="number" step="0.1" class="form-control" id="distancia" name="distancia"
                                               value="<?php echo $route['distancia']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="desnivel" class="form-label">Desnivel (m) *</label>
                                        <input type="number" class="form-control" id="desnivel" name="desnivel"
                                               value="<?php echo $route['desnivel']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="duracion" class="form-label">Duración (h) *</label>
                                        <input type="number" step="0.5" class="form-control" id="duracion" name="duracion"
                                               value="<?php echo $route['duracion']; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="provincia" class="form-label">Provincia *</label>
                                <select class="form-select" id="provincia" name="provincia" required>
                                    <?php foreach (getProvincias() as $prov): ?>
                                        <option value="<?php echo $prov; ?>" <?php echo $route['provincia'] === $prov ? 'selected' : ''; ?>>
                                            <?php echo $prov; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Época recomendada</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="epocas[]" value="primavera" id="primavera"
                                               <?php echo in_array('primavera', $route['epocas']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="primavera">Primavera</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="epocas[]" value="verano" id="verano"
                                               <?php echo in_array('verano', $route['epocas']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="verano">Verano</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="epocas[]" value="otono" id="otono"
                                               <?php echo in_array('otono', $route['epocas']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="otono">Otoño</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="epocas[]" value="invierno" id="invierno"
                                               <?php echo in_array('invierno', $route['epocas']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="invierno">Invierno</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nivel_tecnico" class="form-label">Nivel Técnico (1-5) *</label>
                                        <input type="number" min="1" max="5" class="form-control" id="nivel_tecnico" name="nivel_tecnico"
                                               value="<?php echo $route['nivel_tecnico']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nivel_fisico" class="form-label">Nivel Físico (1-5) *</label>
                                        <input type="number" min="1" max="5" class="form-control" id="nivel_fisico" name="nivel_fisico"
                                               value="<?php echo $route['nivel_fisico']; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción *</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="10" required><?php echo htmlspecialchars($route['descripcion']); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="view.php?id=<?php echo $route['id']; ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
