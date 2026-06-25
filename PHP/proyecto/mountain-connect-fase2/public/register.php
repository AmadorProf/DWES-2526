<?php
/**
 * Formulario de registro de usuarios
 * Tarea 1.2: Sistema de registro con validación
 */
require_once __DIR__ . '/../includes/header.php';

// Redirigir si ya está logueado
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$errors = [];
$success = false;
$formData = [];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar datos
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $nivel_experiencia = sanitizeInput($_POST['nivel_experiencia'] ?? '');
    $especialidad = sanitizeInput($_POST['especialidad'] ?? '');
    $provincia = sanitizeInput($_POST['provincia'] ?? '');
    
    // Guardar datos del formulario para mantenerlos
    $formData = [
        'username' => $username,
        'email' => $email,
        'nivel_experiencia' => $nivel_experiencia,
        'especialidad' => $especialidad,
        'provincia' => $provincia
    ];
    
    // Validaciones
    if (empty($username)) {
        $errors[] = "El nombre de usuario es obligatorio";
    } elseif (strlen($username) < 3) {
        $errors[] = "El nombre de usuario debe tener al menos 3 caracteres";
    }
    
    if (empty($email)) {
        $errors[] = "El email es obligatorio";
    } elseif (!validateEmail($email)) {
        $errors[] = "El formato del email no es válido";
    }
    
    if (empty($password)) {
        $errors[] = "La contraseña es obligatoria";
    } elseif (strlen($password) < 6) {
        $errors[] = "La contraseña debe tener al menos 6 caracteres";
    }
    
    if (empty($confirm_password)) {
        $errors[] = "Debes confirmar la contraseña";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden";
    }
    
    if (empty($nivel_experiencia)) {
        $errors[] = "Debes seleccionar tu nivel de experiencia";
    }
    
    if (empty($provincia)) {
        $errors[] = "Debes seleccionar tu provincia";
    }
    
    // Verificar si el usuario ya existe
    if (empty($errors)) {
        foreach ($_SESSION['users'] as $user) {
            if ($user['username'] === $username) {
                $errors[] = "El nombre de usuario ya está en uso";
                break;
            }
            if ($user['email'] === $email) {
                $errors[] = "El email ya está registrado";
                break;
            }
        }
    }
    
    // Si no hay errores, registrar usuario
    if (empty($errors)) {
        $newUser = [
            'id' => uniqid(),
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'nivel_experiencia' => $nivel_experiencia,
            'especialidad' => $especialidad,
            'provincia' => $provincia,
            'fecha_registro' => date('Y-m-d H:i:s')
        ];
        
        $_SESSION['users'][] = $newUser;
        $success = true;
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-plus"></i> Registro de Usuario</h4>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        ¡Registro exitoso! Ya puedes <a href="login.php">iniciar sesión</a>.
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
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user"></i> Nombre de usuario *
                            </label>
                            <input type="text" class="form-control" id="username" name="username"
                                   value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>"
                                   placeholder="Tu nombre de usuario">
                            <small class="form-text text-muted">Mínimo 3 caracteres</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email *
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
                                   placeholder="tu@email.com">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Contraseña *
                            </label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted">Mínimo 6 caracteres</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-lock"></i> Confirmar contraseña *
                            </label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                        
                        <div class="mb-3">
                            <label for="nivel_experiencia" class="form-label">
                                <i class="fas fa-chart-line"></i> Nivel de experiencia *
                            </label>
                            <select class="form-select" id="nivel_experiencia" name="nivel_experiencia">
                                <option value="">Selecciona tu nivel</option>
                                <option value="principiante" <?php echo ($formData['nivel_experiencia'] ?? '') === 'principiante' ? 'selected' : ''; ?>>Principiante</option>
                                <option value="intermedio" <?php echo ($formData['nivel_experiencia'] ?? '') === 'intermedio' ? 'selected' : ''; ?>>Intermedio</option>
                                <option value="avanzado" <?php echo ($formData['nivel_experiencia'] ?? '') === 'avanzado' ? 'selected' : ''; ?>>Avanzado</option>
                                <option value="experto" <?php echo ($formData['nivel_experiencia'] ?? '') === 'experto' ? 'selected' : ''; ?>>Experto</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="especialidad" class="form-label">
                                <i class="fas fa-mountain"></i> Especialidad
                            </label>
                            <select class="form-select" id="especialidad" name="especialidad">
                                <option value="">Selecciona tu especialidad</option>
                                <option value="senderismo" <?php echo ($formData['especialidad'] ?? '') === 'senderismo' ? 'selected' : ''; ?>>Senderismo</option>
                                <option value="escalada" <?php echo ($formData['especialidad'] ?? '') === 'escalada' ? 'selected' : ''; ?>>Escalada</option>
                                <option value="ferratas" <?php echo ($formData['especialidad'] ?? '') === 'ferratas' ? 'selected' : ''; ?>>Vías Ferratas</option>
                                <option value="alpinismo" <?php echo ($formData['especialidad'] ?? '') === 'alpinismo' ? 'selected' : ''; ?>>Alpinismo</option>
                                <option value="trail" <?php echo ($formData['especialidad'] ?? '') === 'trail' ? 'selected' : ''; ?>>Trail Running</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="provincia" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Provincia *
                            </label>
                            <select class="form-select" id="provincia" name="provincia">
                                <option value="">Selecciona tu provincia</option>
                                <?php foreach (getProvincias() as $prov): ?>
                                    <option value="<?php echo $prov; ?>"
                                            <?php echo ($formData['provincia'] ?? '') === $prov ? 'selected' : ''; ?>>
                                        <?php echo $prov; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus"></i> Registrarse
                            </button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <small>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></small>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
