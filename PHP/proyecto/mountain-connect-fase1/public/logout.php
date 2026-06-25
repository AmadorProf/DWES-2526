<?php
/**
 * Cerrar sesión del usuario
 * Tarea 1.3: Sistema de logout
 */
require_once __DIR__ . '/../config/config.php';

// Eliminar variables de sesión del usuario
unset($_SESSION['user_id']);
unset($_SESSION['user_data']);

// Redirigir a la página principal
header('Location: index.php');
exit();
?>
