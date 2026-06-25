<?php
session_start();
// Array de usuarios predefinidos
$usuarios = [
    [
        'usuario' => 'mario',
        'password' => '1234',
        'nombre_completo' => 'Mario Bros',
        'email' => 'mario@pixelparadise.com',
        'genero_preferido' => 'acción'
    ],
    [
        'usuario' => 'zelda',
        'password' => 'triforce',
        'nombre_completo' => 'Princess Zelda',
        'email' => 'zelda@pixelparadise.com',
        'genero_preferido' => 'aventura'
    ],
    [
        'usuario' => 'cloud',
        'password' => 'ff7',
        'nombre_completo' => 'Cloud Strife',
        'email' => 'cloud@pixelparadise.com',
        'genero_preferido' => 'RPG'
    ]
];

$error = "";

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_input = trim($_POST['usuario'] ?? '');
    $password_input = $_POST['password'] ?? '';
    
    $usuario_encontrado = false;
    
    foreach ($usuarios as $user) {
        if ($user['usuario'] === $usuario_input && $user['password'] === $password_input) {
            // Login exitoso
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['nombre_completo'] = $user['nombre_completo'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['genero_preferido'] = $user['genero_preferido'];
            
            header('Location: tienda.php');
            exit();
        }
    }
    
    $error = "Usuario o contraseña incorrectos. Por favor, intenta de nuevo.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pixel Paradise</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <header>
        <h1>EXAMEN 1EV: Ejercicio 2</h1>
    </header>
    
    <main>
        <h2>Login</h2>
        
        <?php if ($error): ?>
            <div class="mensaje mensaje-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <h3>Iniciar Sesión</h3>
            
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Entrar a la Tienda</button>
        </form>
        
        <div class="panel">
            <h3>Usuarios de Prueba</h3>
            <p><strong>mario</strong> / 1234 (Género: Acción)</p>
            <p><strong>zelda</strong> / triforce (Género: Aventura)</p>
            <p><strong>cloud</strong> / ff7 (Género: RPG)</p>
        </div>
    </main>
</body>
</html>
