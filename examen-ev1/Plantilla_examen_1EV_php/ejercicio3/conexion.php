<?php
// Configuración de la base de datos
$host = 'mysql';
$usuario = 'root';
$password = 'root';
$base_datos = 'biblioteca_magica';

// Crear conexión con MySQLi
$conexion = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer charset UTF-8
$conexion->set_charset("utf8mb4");
?>
