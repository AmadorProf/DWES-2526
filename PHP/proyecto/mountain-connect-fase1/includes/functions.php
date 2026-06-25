<?php
/**
 * Funciones auxiliares reutilizables
 * MountainConnect
 */

/**
 * Sanitiza entrada de texto
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Valida formato de email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Genera nombre único para archivo
 */
function generateUniqueFilename($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

/**
 * Valida archivo de imagen
 */
function validateImage($file) {
    $errors = [];
    
    // Verificar que se subió correctamente
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Error al subir el archivo";
        return $errors;
    }
    
    // Verificar tamaño
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = "El archivo es demasiado grande. Máximo 2MB";
    }
    
    // Verificar extensión
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        $errors[] = "Formato no permitido. Solo JPG, JPEG y PNG";
    }
    
    // Verificar tipo MIME
    $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedMimes)) {
        $errors[] = "El archivo no es una imagen válida";
    }
    
    return $errors;
}

/**
 * Sube un archivo al servidor
 */
function uploadFile($file, $destination) {
    $uniqueName = generateUniqueFilename($file['name']);
    $targetPath = $destination . $uniqueName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $uniqueName;
    }
    return false;
}

/**
 * Verifica si el usuario está logueado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Obtiene el usuario actual
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return $_SESSION['user_data'] ?? null;
    }
    return null;
}

/**
 * Requiere autenticación (redirige si no está logueado)
 */
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit();
    }
}

/**
 * Formatea dificultad numérica a texto
 */
function getDifficultyText($level) {
    $difficulties = [
        1 => 'Fácil',
        2 => 'Moderada',
        3 => 'Difícil',
        4 => 'Muy Difícil',
        5 => 'Experto'
    ];
    return $difficulties[$level] ?? 'Desconocida';
}

/**
 * Formatea fecha en español
 */
function formatDate($date) {
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date('d/m/Y H:i', $timestamp);
}

/**
 * Array de provincias españolas
 */
function getProvincias() {
    return [
        'A Coruña', 'Álava', 'Albacete', 'Alicante', 'Almería', 'Asturias',
        'Ávila', 'Badajoz', 'Barcelona', 'Burgos', 'Cáceres', 'Cádiz',
        'Cantabria', 'Castellón', 'Ciudad Real', 'Córdoba', 'Cuenca',
        'Girona', 'Granada', 'Guadalajara', 'Guipúzcoa', 'Huelva', 'Huesca',
        'Islas Baleares', 'Jaén', 'La Rioja', 'Las Palmas', 'León', 'Lleida',
        'Lugo', 'Madrid', 'Málaga', 'Murcia', 'Navarra', 'Ourense', 'Palencia',
        'Pontevedra', 'Salamanca', 'Santa Cruz de Tenerife', 'Segovia',
        'Sevilla', 'Soria', 'Tarragona', 'Teruel', 'Toledo', 'Valencia',
        'Valladolid', 'Vizcaya', 'Zamora', 'Zaragoza'
    ];
}

/**
 * Inicializa datos de sesión si no existen
 */
function initSessionData() {
    if (!isset($_SESSION['users'])) {
        $_SESSION['users'] = [];
    }
    if (!isset($_SESSION['routes'])) {
        $_SESSION['routes'] = [];
    }
}
?>
