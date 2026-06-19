<?php
/**
 * Formulario de creación de rutas con galería de fotos
 * Tarea 1.5: Creación de rutas con subida de imágenes
 */
require_once __DIR__ . '/../../includes/header.php';

// Verificar autenticación
requireAuth();

$errors = [];
$success = false;
$formData = [];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
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
    
    // Guardar datos para mantener el formulario
    $formData = [
        'nombre' => $nombre,
        'dificultad' => $dificultad,
        'distancia' => $distancia,
        'desnivel' => $desnivel,
        'duracion' => $duracion,
        'provincia' => $provincia,
        'epocas' => $epocas,
        'descripcion' => $descripcion,
        'nivel_tecnico' => $nivel_tecnico,
        'nivel_fisico' => $nivel_fisico
    ];
    
    // Validaciones
    if (empty($nombre)) {
        $errors[] = "El nombre de la ruta es obligatorio";
    }
    
    if (empty($dificultad)) {
        $errors[] = "Debes seleccionar la dificultad";
    }
    
    if (empty($distancia) || !is_numeric($distancia) || $distancia <= 0) {
        $errors[] = "La distancia debe ser un número mayor que 0";
    }
    
    if (empty($desnivel) || !is_numeric($desnivel) || $desnivel < 0) {
        $errors[] = "El desnivel debe ser un número positivo";
    }
    
    if (empty($duracion) || !is_numeric($duracion) || $duracion <= 0) {
        $errors[] = "La duración debe ser un número mayor que 0";
    }
    
    if (empty($provincia)) {
        $errors[] = "Debes seleccionar una provincia";
    }
    
    if (empty($descripcion)) {
        $errors[] = "La descripción es obligatoria";
    } elseif (strlen($descripcion) < 20) {
        $errors[] = "La descripción debe tener al menos 20 caracteres";
    }
    
    if (empty($nivel_tecnico) || $nivel_tecnico < 1 || $nivel_tecnico > 5) {
        $errors[] = "El nivel técnico debe estar entre 1 y 5";
    }
    
    if (empty($nivel_fisico) || $nivel_fisico < 1 || $nivel_fisico > 5) {
        $errors[] = "El nivel físico debe estar entre 1 y 5";
    }
    
    // Procesar imágenes
    $uploadedPhotos = [];
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['name'] as $key => $filename) {
            if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                $photoFile = [
                    'name' => $_FILES['photos']['name'][$key],
                    'type' => $_FILES['photos']['type'][$key],
                    'tmp_name' => $_FILES['photos']['tmp_name'][$key],
                    'error' => $_FILES['photos']['error'][$key],
                    'size' => $_FILES['photos']['size'][$key]
                ];
                
                // Validar imagen
                $photoErrors = validateImage($photoFile);
                if (!empty($photoErrors)) {
                    $errors = array_merge($errors, $photoErrors);
                } else {
                    // Subir imagen
                    $uploadedFilename = uploadFile($photoFile, PHOTO_DIR);
                    if ($uploadedFilename) {
                        $uploadedPhotos[] = $uploadedFilename;
                    } else {
                        $errors[] = "Error al subir la imagen: " . htmlspecialchars($filename);
                    }
                }
            }
        }
    }
    
    // Si no hay errores, guardar la ruta
    if (empty($errors)) {
        $user = getCurrentUser();
        
        $newRoute = [
            'id' => uniqid(),
            'user_id' => $user['id'],
            'username' => $user['username'],
            'nombre' => $nombre,
            'dificultad' => $dificultad,
            'distancia' => $distancia,
            'desnivel' => $desnivel,
            'duracion' => $duracion,
            'provincia' => $provincia,
            'epocas' => $epocas,
            'descripcion' => $descripcion,
            'nivel_tecnico' => $nivel_tecnico,
            'nivel_fisico' => $nivel_fisico,
            'fotos' => $uploadedPhotos,
            'fecha_creacion' => time()
        ];
        
        if (!isset($_SESSION['routes'])) {
            $_SESSION['routes'] = [];
        }
        $_SESSION['routes'][] = $newRoute;
        
        $success = true;
    }
}
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Crear Nueva Ruta</h4>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        ¡Ruta creada exitosamente!
                        <a href="list.php">Ver todas las rutas</a> o
                        <a href="create.php">crear otra ruta</a>
                    </div>
                <?php else: ?>
                    
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
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-info-circle"></i> Información Básica</h5>
                                
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre de la ruta *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                           value="<?php echo htmlspecialchars($formData['nombre'] ?? ''); ?>"
                                           placeholder="Ej: Ascensión al Pico Aneto" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="dificultad" class="form-label">Dificultad *</label>
                                    <select class="form-select" id="dificultad" name="dificultad" required>
                                        <option value="">Selecciona dificultad</option>
                                        <option value="facil" <?php echo ($formData['dificultad'] ?? '') === 'facil' ? 'selected' : ''; ?>>Fácil</option>
                                        <option value="moderada" <?php echo ($formData['dificultad'] ?? '') === 'moderada' ? 'selected' : ''; ?>>Moderada</option>
                                        <option value="dificil" <?php echo ($formData['dificultad'] ?? '') === 'dificil' ? 'selected' : ''; ?>>Difícil</option>
                                        <option value="muy_dificil" <?php echo ($formData['dificultad'] ?? '') === 'muy_dificil' ? 'selected' : ''; ?>>Muy Difícil</option>
                                    </select>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="distancia" class="form-label">Distancia (km) *</label>
                                            <input type="number" step="0.1" class="form-control" id="distancia" name="distancia"
                                                   value="<?php echo htmlspecialchars($formData['distancia'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="desnivel" class="form-label">Desnivel (m) *</label>
                                            <input type="number" class="form-control" id="desnivel" name="desnivel"
                                                   value="<?php echo htmlspecialchars($formData['desnivel'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="duracion" class="form-label">Duración (h) *</label>
                                            <input type="number" step="0.5" class="form-control" id="duracion" name="duracion"
                                                   value="<?php echo htmlspecialchars($formData['duracion'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="provincia" class="form-label">Provincia *</label>
                                    <select class="form-select" id="provincia" name="provincia" required>
                                        <option value="">Selecciona provincia</option>
                                        <?php foreach (getProvincias() as $prov): ?>
                                            <option value="<?php echo $prov; ?>"
                                                    <?php echo ($formData['provincia'] ?? '') === $prov ? 'selected' : ''; ?>>
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
                                                   <?php echo in_array('primavera', $formData['epocas'] ?? []) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="primavera">Primavera</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="epocas[]" value="verano" id="verano"
                                                   <?php echo in_array('verano', $formData['epocas'] ?? []) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="verano">Verano</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="epocas[]" value="otono" id="otono"
                                                   <?php echo in_array('otono', $formData['epocas'] ?? []) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="otono">Otoño</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="epocas[]" value="invierno" id="invierno"
                                                   <?php echo in_array('invierno', $formData['epocas'] ?? []) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="invierno">Invierno</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-chart-line"></i> Niveles y Descripción</h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nivel_tecnico" class="form-label">Nivel Técnico (1-5) *</label>
                                            <input type="number" min="1" max="5" class="form-control" id="nivel_tecnico" name="nivel_tecnico"
                                                   value="<?php echo htmlspecialchars($formData['nivel_tecnico'] ?? ''); ?>" required>
                                            <small class="text-muted">1=Principiante, 5=Experto</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nivel_fisico" class="form-label">Nivel Físico (1-5) *</label>
                                            <input type="number" min="1" max="5" class="form-control" id="nivel_fisico" name="nivel_fisico"
                                                   value="<?php echo htmlspecialchars($formData['nivel_fisico'] ?? ''); ?>" required>
                                            <small class="text-muted">1=Bajo, 5=Muy Alto</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción *</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="5"
                                              placeholder="Describe la ruta, puntos de interés, recomendaciones..." required><?php echo htmlspecialchars($formData['descripcion'] ?? ''); ?></textarea>
                                    <small class="text-muted">Mínimo 20 caracteres</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="photos" class="form-label">
                                        <i class="fas fa-images"></i> Fotografías
                                    </label>
                                    <input type="file" class="form-control" id="photos" name="photos[]"
                                           accept="image/jpeg,image/jpg,image/png" multiple>
                                    <small class="text-muted">
                                        Puedes subir múltiples imágenes. Formatos: JPG, PNG. Máximo: 2MB por imagen
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Crear Ruta
                            </button>
                            <a href="list.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
