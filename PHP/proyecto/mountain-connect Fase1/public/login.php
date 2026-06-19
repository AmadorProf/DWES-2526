<?php
/**
 * Formulario de inicio de sesión
 * Tarea 1.3: Sistema de login y sesiones
 */
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirigir si ya está logueado
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$errors = [];
$loginValue = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = sanitizeInput($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $loginValue = $login;

    if (empty($login)) {
        $errors[] = "Debes ingresar tu usuario o email";
    }
    if (empty($password)) {
        $errors[] = "Debes ingresar tu contraseña";
    }

    if (empty($errors)) {
        $userFound = false;
        foreach ($_SESSION['users'] as $user) {
            if ($user['username'] === $login || $user['email'] === $login) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_data'] = [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'nivel_experiencia' => $user['nivel_experiencia'],
                        'especialidad' => $user['especialidad'],
                        'provincia' => $user['provincia']
                    ];
                    header('Location: index.php');
                    exit();
                } else {
                    $errors[] = "Contraseña incorrecta";
                }
                $userFound = true;
                break;
            }
        }
        if (!$userFound) {
            $errors[] = "Usuario o email no encontrado";
        }
    }
}

// ahora incluimos la cabecera (que imprime HTML)
require_once __DIR__ . '/../includes/header.php';
?>
<!-- el resto de tu HTML (lo que ya tienes) -->


<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Error:</strong>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        ¡Registro completado! Ya puedes iniciar sesión.
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="login" class="form-label">
                            <i class="fas fa-user"></i> Usuario o Email
                        </label>
                        <input type="text" class="form-control" id="login" name="login"
                               value="<?php echo htmlspecialchars($loginValue); ?>"
                               placeholder="Tu usuario o email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Contraseña
                        </label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Tu contraseña" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">
                            Recordarme
                        </label>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </button>
                    </div>
                    
                    <div class="text-center mt-3">
                        <small>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></small>
                    </div>
                    
                    <div class="text-center mt-2">
                        <small><a href="#" class="text-muted">¿Olvidaste tu contraseña?</a></small>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Información de demo -->
        <div class="card mt-3 border-info">
            <div class="card-body bg-light">
                <h6 class="text-info"><i class="fas fa-info-circle"></i> Demo</h6>
                <p class="small mb-0">
                    Esta es una versión de demostración. Los datos se almacenan temporalmente en sesión.
                    Si no tienes cuenta, <a href="register.php">regístrate primero</a>.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
