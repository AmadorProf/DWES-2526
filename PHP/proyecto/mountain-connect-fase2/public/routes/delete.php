<?php
/**
 * Eliminar ruta
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

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

// Eliminar fotos asociadas
if (!empty($route['fotos'])) {
    foreach ($route['fotos'] as $foto) {
        $photoPath = PHOTO_DIR . $foto;
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
    }
}

// Eliminar la ruta del array
unset($_SESSION['routes'][$routeIndex]);
$_SESSION['routes'] = array_values($_SESSION['routes']); // Reindexar el array

// Redirigir al listado con mensaje
header('Location: list.php?deleted=1');
exit();
?>
