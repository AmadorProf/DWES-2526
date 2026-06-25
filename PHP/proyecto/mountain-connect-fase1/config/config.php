<?php
/**
 * Archivo de configuración general del proyecto
 * MountainConnect - Plataforma Web Montañera
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de la base de datos (para fase 2)
define('DB_HOST', 'localhost');
define('DB_NAME', 'mountain_connect');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de la aplicación
define('SITE_NAME', 'MountainConnect');
define('SITE_URL', 'http://localhost:8080');

// Configuración de uploads
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('PHOTO_DIR', UPLOAD_DIR . 'photos/');
define('PROFILE_DIR', UPLOAD_DIR . 'profiles/');
define('MAX_FILE_SIZE', 2097152); // 2MB en bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png']);

// Crear directorios si no existen
if (!file_exists(PHOTO_DIR)) {
    mkdir(PHOTO_DIR, 0777, true);
}
if (!file_exists(PROFILE_DIR)) {
    mkdir(PROFILE_DIR, 0777, true);
}

// Zona horaria
date_default_timezone_set('Europe/Madrid');
?>
