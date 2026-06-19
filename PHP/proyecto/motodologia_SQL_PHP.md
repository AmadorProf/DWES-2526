# Guía de Enseñanza: MySQLi, Conexión a Bases de Datos y POO
## Proyecto Mountain Connect - Fase 2

---

## 🎯 Estrategia de Explicación Recomendada

### **Etapa 1: Fundamentos de Base de Datos (Antes de código)**
**Duración: 1-2 sesiones**

#### 1. Diseño y normalización
- Explica el diagrama ER del documento
- Muestra las relaciones entre tablas
- Justifica por qué está normalizado (evitar redundancia)
- Ejecuta el script SQL y muestra las tablas creadas en phpMyAdmin

#### 2. Conceptos de seguridad SQL
- Demuestra un SQL Injection con ejemplo sencillo (concatenación)
- Explica por qué es peligroso
- Introduce la solución: consultas preparadas

**Ejemplo de SQL Injection (NO HACER):**
```php
// ❌ VULNERABLE
$username = $_POST['username'];
$sql = "SELECT * FROM usuarios WHERE username = '$username'";
// Si username = "admin' OR '1'='1" → acceso sin contraseña
```

---

### **Etapa 2: Conexión y Primeras Consultas (Procedimental)**
**Duración: 2-3 sesiones**

#### **Sesión 2.1: Conexión básica procedimental (Tarea 2.1)**

**Objetivo:** Entender los pasos de conexión y gestión de errores.

```php
// config/database.php - Versión procedimental inicial

<?php
// Constantes de configuración
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mountain_connect');

// Función de conexión
function conectarDB() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Gestión de errores
    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    
    // Configurar charset UTF-8
    mysqli_set_charset($conn, "utf8");
    
    return $conn;
}

// Función para cerrar conexión
function cerrarDB($conn) {
    mysqli_close($conn);
}
?>
```

**Página de prueba:**
```php
// test_connection.php
<?php
require_once 'config/database.php';

$conn = conectarDB();

if ($conn) {
    echo "✅ Conexión exitosa a la base de datos";
    
    // Mostrar info del servidor
    echo "<br>Servidor: " . mysqli_get_host_info($conn);
    echo "<br>Versión: " . mysqli_get_server_info($conn);
}

cerrarDB($conn);
?>
```

**Puntos a explicar:**
- Cada parámetro de `mysqli_connect()`
- Por qué `utf8` es importante
- Cuándo cerrar conexiones
- Qué hacer si la conexión falla

---

#### **Sesión 2.2: Primera consulta SELECT simple**

```php
// Ejemplo: Listar todos los usuarios
<?php
require_once 'config/database.php';

$conn = conectarDB();

// Consulta simple (sin parámetros externos)
$sql = "SELECT id, username, email FROM usuarios";
$resultado = mysqli_query($conn, $sql);

// Verificar si hay resultados
if (mysqli_num_rows($resultado) > 0) {
    echo "<h2>Usuarios registrados:</h2>";
    echo "<ul>";
    
    // Recorrer resultados
    while ($usuario = mysqli_fetch_assoc($resultado)) {
        echo "<li>";
        echo "ID: " . $usuario['id'] . " - ";
        echo "Usuario: " . $usuario['username'] . " - ";
        echo "Email: " . $usuario['email'];
        echo "</li>";
    }
    
    echo "</ul>";
} else {
    echo "No hay usuarios registrados";
}

// Liberar resultado
mysqli_free_result($resultado);
cerrarDB($conn);
?>
```

**Conceptos a enseñar:**
- `mysqli_query()` ejecuta consultas
- `mysqli_num_rows()` cuenta resultados
- `mysqli_fetch_assoc()` obtiene fila como array asociativo
- Diferencia entre `fetch_assoc()`, `fetch_array()`, `fetch_object()`
- Siempre liberar resultados

---

#### **Sesión 2.3: Login con consultas preparadas (Tarea 2.3)**

**Objetivo:** Introducir consultas preparadas para prevenir SQL Injection.

```php
// public/login.php (versión con MySQLi)

<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $conn = conectarDB();
    
    // ✅ Consulta preparada (SEGURA)
    $sql = "SELECT id, username, email, password, nivel_experiencia 
            FROM usuarios 
            WHERE username = ? OR email = ?";
    
    // Preparar
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        // Vincular parámetros (s = string)
        mysqli_stmt_bind_param($stmt, "ss", $username, $username);
        
        // Ejecutar
        mysqli_stmt_execute($stmt);
        
        // Obtener resultado
        $resultado = mysqli_stmt_get_result($stmt);
        
        if ($usuario = mysqli_fetch_assoc($resultado)) {
            // Verificar contraseña
            if (password_verify($password, $usuario['password'])) {
                // Login exitoso
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['username'] = $usuario['username'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['nivel_experiencia'] = $usuario['nivel_experiencia'];
                
                // Actualizar último acceso
                $sql_update = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
                $stmt_update = mysqli_prepare($conn, $sql_update);
                mysqli_stmt_bind_param($stmt_update, "i", $usuario['id']);
                mysqli_stmt_execute($stmt_update);
                mysqli_stmt_close($stmt_update);
                
                header("Location: profile.php");
                exit();
            } else {
                $error = "Credenciales incorrectas";
            }
        } else {
            $error = "Credenciales incorrectas";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error en la consulta";
    }
    
    cerrarDB($conn);
}
?>
```

**Explicar paso a paso:**
1. `mysqli_prepare()` - prepara la consulta con placeholders `?`
2. `mysqli_stmt_bind_param()` - vincula variables a placeholders
   - Primer parámetro: tipos (s=string, i=integer, d=double, b=blob)
   - Siguientes: variables en orden
3. `mysqli_stmt_execute()` - ejecuta la consulta
4. `mysqli_stmt_get_result()` - obtiene resultados
5. `password_verify()` - verifica hash de contraseña

**Tipos de bind_param:**
- `s` - string
- `i` - integer
- `d` - double/float
- `b` - blob (archivos binarios)

---

#### **Sesión 2.4: Registro con INSERT (Tarea 2.2)**

```php
// public/register.php

<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $nivel_experiencia = $_POST['nivel_experiencia'];
    $provincia = $_POST['provincia'];
    
    $errores = [];
    
    $conn = conectarDB();
    
    // Validar username único
    $sql = "SELECT id FROM usuarios WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $errores[] = "El nombre de usuario ya existe";
    }
    mysqli_stmt_close($stmt);
    
    // Validar email único
    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $errores[] = "El email ya está registrado";
    }
    mysqli_stmt_close($stmt);
    
    // Si no hay errores, insertar
    if (empty($errores)) {
        // Hash de contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (username, email, password, nivel_experiencia, provincia) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", 
            $username, $email, $password_hash, $nivel_experiencia, $provincia);
        
        if (mysqli_stmt_execute($stmt)) {
            // Obtener ID del usuario insertado
            $user_id = mysqli_insert_id($conn);
            
            echo "✅ Usuario registrado con éxito. ID: $user_id";
            header("Location: login.php");
            exit();
        } else {
            $errores[] = "Error al registrar: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    }
    
    cerrarDB($conn);
}
?>
```

**Conceptos clave:**
- `mysqli_stmt_store_result()` + `mysqli_stmt_num_rows()` para verificar existencia
- `password_hash()` con `PASSWORD_DEFAULT` (bcrypt)
- `mysqli_insert_id()` obtiene el ID autoincremental generado
- Validar antes de insertar

---

### **Etapa 3: Introducción a POO (Transición natural)**
**Duración: 2-3 sesiones**

#### **Sesión 3.1: Refactorizar a clase Database**

**Objetivo:** Encapsular la lógica de conexión y consultas.

```php
// config/Database.php

<?php
class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $dbname = 'mountain_connect';
    private $conn;
    
    // Constructor: se ejecuta al crear objeto
    public function __construct() {
        $this->conectar();
    }
    
    // Método privado de conexión
    private function conectar() {
        $this->conn = mysqli_connect(
            $this->host, 
            $this->user, 
            $this->pass, 
            $this->dbname
        );
        
        if (!$this->conn) {
            die("Error de conexión: " . mysqli_connect_error());
        }
        
        mysqli_set_charset($this->conn, "utf8");
    }
    
    // Obtener conexión
    public function getConexion() {
        return $this->conn;
    }
    
    // Consulta preparada genérica
    public function query($sql, $tipos = "", $params = []) {
        $stmt = mysqli_prepare($this->conn, $sql);
        
        if (!$stmt) {
            throw new Exception("Error en prepare: " . mysqli_error($this->conn));
        }
        
        // Si hay parámetros, vincularlos
        if (!empty($tipos) && !empty($params)) {
            mysqli_stmt_bind_param($stmt, $tipos, ...$params);
        }
        
        mysqli_stmt_execute($stmt);
        
        return $stmt;
    }
    
    // SELECT que retorna un solo registro
    public function fetchOne($sql, $tipos = "", $params = []) {
        $stmt = $this->query($sql, $tipos, $params);
        $resultado = mysqli_stmt_get_result($stmt);
        $fila = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($stmt);
        
        return $fila;
    }
    
    // SELECT que retorna múltiples registros
    public function fetchAll($sql, $tipos = "", $params = []) {
        $stmt = $this->query($sql, $tipos, $params);
        $resultado = mysqli_stmt_get_result($stmt);
        
        $filas = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $filas[] = $fila;
        }
        
        mysqli_stmt_close($stmt);
        return $filas;
    }
    
    // INSERT/UPDATE/DELETE
    public function execute($sql, $tipos = "", $params = []) {
        $stmt = $this->query($sql, $tipos, $params);
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        
        return $affected;
    }
    
    // Obtener último ID insertado
    public function lastInsertId() {
        return mysqli_insert_id($this->conn);
    }
    
    // Iniciar transacción
    public function beginTransaction() {
        mysqli_begin_transaction($this->conn);
    }
    
    // Confirmar transacción
    public function commit() {
        mysqli_commit($this->conn);
    }
    
    // Revertir transacción
    public function rollback() {
        mysqli_rollback($this->conn);
    }
    
    // Destructor: cerrar conexión
    public function __destruct() {
        if ($this->conn) {
            mysqli_close($this->conn);
        }
    }
}
?>
```

**Ventajas de la clase:**
- ✅ Código reutilizable
- ✅ Encapsulación (propiedades privadas)
- ✅ Métodos especializados (fetchOne, fetchAll, execute)
- ✅ Gestión automática de conexión (constructor/destructor)
- ✅ Transacciones incluidas

---

#### **Sesión 3.2: Crear clase Usuario**

```php
// models/Usuario.php

<?php
require_once '../config/Database.php';

class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Método de login
    public function login($username, $password) {
        $sql = "SELECT id, username, email, password, nivel_experiencia, rol 
                FROM usuarios 
                WHERE (username = ? OR email = ?) AND activo = 1";
        
        $usuario = $this->db->fetchOne($sql, "ss", [$username, $username]);
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            // Actualizar último acceso
            $this->actualizarUltimoAcceso($usuario['id']);
            
            // Eliminar contraseña del array
            unset($usuario['password']);
            
            return $usuario;
        }
        
        return false;
    }
    
    // Método de registro
    public function registrar($datos) {
        // Validar que username y email sean únicos
        if ($this->existeUsername($datos['username'])) {
            throw new Exception("El nombre de usuario ya existe");
        }
        
        if ($this->existeEmail($datos['email'])) {
            throw new Exception("El email ya está registrado");
        }
        
        // Hash de contraseña
        $passwordHash = password_hash($datos['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (username, email, password, nivel_experiencia, 
                especialidad, provincia, biografia) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $datos['username'],
            $datos['email'],
            $passwordHash,
            $datos['nivel_experiencia'],
            $datos['especialidad'] ?? null,
            $datos['provincia'],
            $datos['biografia'] ?? null
        ];
        
        $this->db->execute($sql, "sssssss", $params);
        
        return $this->db->lastInsertId();
    }
    
    // Verificar si existe username
    private function existeUsername($username) {
        $sql = "SELECT id FROM usuarios WHERE username = ?";
        $resultado = $this->db->fetchOne($sql, "s", [$username]);
        return $resultado !== null;
    }
    
    // Verificar si existe email
    private function existeEmail($email) {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $resultado = $this->db->fetchOne($sql, "s", [$email]);
        return $resultado !== null;
    }
    
    // Obtener usuario por ID
    public function obtenerPorId($id) {
        $sql = "SELECT id, username, email, nivel_experiencia, especialidad, 
                provincia, foto_perfil, biografia, fecha_registro, ultimo_acceso 
                FROM usuarios 
                WHERE id = ?";
        
        return $this->db->fetchOne($sql, "i", [$id]);
    }
    
    // Actualizar último acceso
    private function actualizarUltimoAcceso($id) {
        $sql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
        $this->db->execute($sql, "i", [$id]);
    }
    
    // Actualizar perfil
    public function actualizarPerfil($id, $datos) {
        $sql = "UPDATE usuarios 
                SET biografia = ?, nivel_experiencia = ?, 
                    especialidad = ?, provincia = ? 
                WHERE id = ?";
        
        $params = [
            $datos['biografia'],
            $datos['nivel_experiencia'],
            $datos['especialidad'],
            $datos['provincia'],
            $id
        ];
        
        return $this->db->execute($sql, "ssssi", $params);
    }
    
    // Cambiar contraseña
    public function cambiarPassword($id, $passwordActual, $passwordNueva) {
        // Verificar contraseña actual
        $sql = "SELECT password FROM usuarios WHERE id = ?";
        $usuario = $this->db->fetchOne($sql, "i", [$id]);
        
        if (!password_verify($passwordActual, $usuario['password'])) {
            throw new Exception("La contraseña actual no es correcta");
        }
        
        // Actualizar con nueva contraseña
        $passwordHash = password_hash($passwordNueva, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
        
        return $this->db->execute($sql, "si", [$passwordHash, $id]);
    }
    
    // Obtener estadísticas del usuario
    public function obtenerEstadisticas($id) {
        $stats = [];
        
        // Contar rutas
        $sql = "SELECT COUNT(*) as total FROM rutas WHERE user_id = ?";
        $resultado = $this->db->fetchOne($sql, "i", [$id]);
        $stats['rutas'] = $resultado['total'];
        
        // Contar fotos
        $sql = "SELECT COUNT(*) as total FROM fotografias WHERE user_id = ?";
        $resultado = $this->db->fetchOne($sql, "i", [$id]);
        $stats['fotos'] = $resultado['total'];
        
        // Contar comentarios
        $sql = "SELECT COUNT(*) as total FROM comentarios WHERE user_id = ?";
        $resultado = $this->db->fetchOne($sql, "i", [$id]);
        $stats['comentarios'] = $resultado['total'];
        
        return $stats;
    }
}
?>
```

**Uso de la clase Usuario:**

```php
// public/login.php - Versión con POO

<?php
session_start();
require_once '../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    try {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->login($username, $password);
        
        if ($usuario) {
            // Guardar en sesión
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['username'] = $usuario['username'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['nivel_experiencia'] = $usuario['nivel_experiencia'];
            $_SESSION['rol'] = $usuario['rol'];
            
            header("Location: profile.php");
            exit();
        } else {
            $error = "Credenciales incorrectas";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
```

**Ventajas del enfoque POO:**
- ✅ Separación de responsabilidades
- ✅ Código más organizado y mantenible
- ✅ Métodos reutilizables
- ✅ Fácil de testear
- ✅ Lógica de negocio encapsulada

---

### **Etapa 4: CRUD Completo y Conceptos Avanzados**
**Duración: 3-4 sesiones**

#### **Sesión 4.1: CRUD de Rutas con Transacciones (Tarea 2.4)**

```php
// models/Ruta.php

<?php
require_once '../config/Database.php';

class Ruta {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Crear ruta con transacción
    public function crear($datos, $epocas = [], $fotos = []) {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();
            
            // 1. Insertar ruta
            $sql = "INSERT INTO rutas (user_id, nombre, dificultad, distancia, 
                    desnivel, duracion, provincia, descripcion, nivel_tecnico, nivel_fisico) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $datos['user_id'],
                $datos['nombre'],
                $datos['dificultad'],
                $datos['distancia'],
                $datos['desnivel'],
                $datos['duracion'],
                $datos['provincia'],
                $datos['descripcion'],
                $datos['nivel_tecnico'],
                $datos['nivel_fisico']
            ];
            
            $this->db->execute($sql, "issdidssis", $params);
            $rutaId = $this->db->lastInsertId();
            
            // 2. Insertar épocas recomendadas
            if (!empty($epocas)) {
                $sqlEpoca = "INSERT INTO rutas_epocas (ruta_id, epoca) VALUES (?, ?)";
                foreach ($epocas as $epoca) {
                    $this->db->execute($sqlEpoca, "is", [$rutaId, $epoca]);
                }
            }
            
            // 3. Insertar fotos
            if (!empty($fotos)) {
                $sqlFoto = "INSERT INTO fotografias (ruta_id, user_id, nombre_archivo, titulo, orden) 
                            VALUES (?, ?, ?, ?, ?)";
                $orden = 1;
                foreach ($fotos as $foto) {
                    $params = [
                        $rutaId,
                        $datos['user_id'],
                        $foto['nombre_archivo'],
                        $foto['titulo'] ?? null,
                        $orden++
                    ];
                    $this->db->execute($sqlFoto, "iissi", $params);
                }
            }
            
            // Confirmar transacción
            $this->db->commit();
            
            return $rutaId;
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->db->rollback();
            throw new Exception("Error al crear ruta: " . $e->getMessage());
        }
    }
    
    // Listar rutas con paginación y filtros
    public function listar($pagina = 1, $porPagina = 10, $filtros = []) {
        $offset = ($pagina - 1) * $porPagina;
        
        // Construir WHERE dinámicamente
        $where = ["r.activa = 1"];
        $tipos = "";
        $params = [];
        
        if (!empty($filtros['dificultad'])) {
            $where[] = "r.dificultad = ?";
            $tipos .= "s";
            $params[] = $filtros['dificultad'];
        }
        
        if (!empty($filtros['provincia'])) {
            $where[] = "r.provincia = ?";
            $tipos .= "s";
            $params[] = $filtros['provincia'];
        }
        
        if (!empty($filtros['nivel_tecnico'])) {
            $where[] = "r.nivel_tecnico <= ?";
            $tipos .= "i";
            $params[] = $filtros['nivel_tecnico'];
        }
        
        $whereSQL = implode(" AND ", $where);
        
        // Consulta principal
        $sql = "SELECT r.*, u.username, 
                (SELECT nombre_archivo FROM fotografias WHERE ruta_id = r.id ORDER BY orden LIMIT 1) as foto_portada
                FROM rutas r
                INNER JOIN usuarios u ON r.user_id = u.id
                WHERE $whereSQL
                ORDER BY r.fecha_creacion DESC
                LIMIT ? OFFSET ?";
        
        // Añadir límite y offset a parámetros
        $tipos .= "ii";
        $params[] = $porPagina;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $tipos, $params);
    }
    
    // Obtener detalle completo de ruta
    public function obtenerPorId($id) {
        $sql = "SELECT r.*, u.username, u.foto_perfil as user_foto
                FROM rutas r
                INNER JOIN usuarios u ON r.user_id = u.id
                WHERE r.id = ?";
        
        $ruta = $this->db->fetchOne($sql, "i", [$id]);
        
        if ($ruta) {
            // Cargar épocas
            $ruta['epocas'] = $this->obtenerEpocas($id);
            
            // Cargar fotos
            $ruta['fotos'] = $this->obtenerFotos($id);
            
            // Incrementar visitas
            $this->incrementarVisitas($id);
        }
        
        return $ruta;
    }
    
    // Obtener épocas de una ruta
    private function obtenerEpocas($rutaId) {
        $sql = "SELECT epoca FROM rutas_epocas WHERE ruta_id = ?";
        $epocas = $this->db->fetchAll($sql, "i", [$rutaId]);
        
        // Convertir a array simple
        return array_column($epocas, 'epoca');
    }
    
    // Obtener fotos de una ruta
    private function obtenerFotos($rutaId) {
        $sql = "SELECT * FROM fotografias WHERE ruta_id = ? ORDER BY orden";
        return $this->db->fetchAll($sql, "i", [$rutaId]);
    }
    
    // Incrementar contador de visitas
    private function incrementarVisitas($rutaId) {
        $sql = "UPDATE rutas SET visitas = visitas + 1 WHERE id = ?";
        $this->db->execute($sql, "i", [$rutaId]);
    }
    
    // Actualizar ruta
    public function actualizar($id, $datos, $epocas = []) {
        try {
            $this->db->beginTransaction();
            
            // Actualizar datos de ruta
            $sql = "UPDATE rutas 
                    SET nombre = ?, dificultad = ?, distancia = ?, desnivel = ?, 
                        duracion = ?, provincia = ?, descripcion = ?, 
                        nivel_tecnico = ?, nivel_fisico = ?, fecha_modificacion = NOW()
                    WHERE id = ?";
            
            $params = [
                $datos['nombre'],
                $datos['dificultad'],
                $datos['distancia'],
                $datos['desnivel'],
                $datos['duracion'],
                $datos['provincia'],
                $datos['descripcion'],
                $datos['nivel_tecnico'],
                $datos['nivel_fisico'],
                $id
            ];
            
            $this->db->execute($sql, "ssdiisssii", $params);
            
            // Actualizar épocas: eliminar y reinsertar
            $sqlDelete = "DELETE FROM rutas_epocas WHERE ruta_id = ?";
            $this->db->execute($sqlDelete, "i", [$id]);
            
            if (!empty($epocas)) {
                $sqlEpoca = "INSERT INTO rutas_epocas (ruta_id, epoca) VALUES (?, ?)";
                foreach ($epocas as $epoca) {
                    $this->db->execute($sqlEpoca, "is", [$id, $epoca]);
                }
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Error al actualizar ruta: " . $e->getMessage());
        }
    }
    
    // Eliminar ruta
    public function eliminar($id) {
        // Obtener fotos para eliminar archivos físicos
        $fotos = $this->obtenerFotos($id);
        
        // Eliminar de BD (CASCADE eliminará relaciones)
        $sql = "DELETE FROM rutas WHERE id = ?";
        $this->db->execute($sql, "i", [$id]);
        
        // Eliminar archivos físicos
        foreach ($fotos as $foto) {
            $ruta_archivo = "../uploads/photos/" . $foto['nombre_archivo'];
            if (file_exists($ruta_archivo)) {
                unlink($ruta_archivo);
            }
        }
        
        return true;
    }
    
    // Verificar si usuario es propietario
    public function esPropietario($rutaId, $userId) {
        $sql = "SELECT id FROM rutas WHERE id = ? AND user_id = ?";
        $resultado = $this->db->fetchOne($sql, "ii", [$rutaId, $userId]);
        return $resultado !== null;
    }
    
    // Contar total de rutas (para paginación)
    public function contarTotal($filtros = []) {
        $where = ["activa = 1"];
        $tipos = "";
        $params = [];
        
        if (!empty($filtros['dificultad'])) {
            $where[] = "dificultad = ?";
            $tipos .= "s";
            $params[] = $filtros['dificultad'];
        }
        
        if (!empty($filtros['provincia'])) {
            $where[] = "provincia = ?";
            $tipos .= "s";
            $params[] = $filtros['provincia'];
        }
        
        $whereSQL = implode(" AND ", $where);
        $sql = "SELECT COUNT(*) as total FROM rutas WHERE $whereSQL";
        
        $resultado = empty($tipos) ? 
            $this->db->fetchOne($sql) : 
            $this->db->fetchOne($sql, $tipos, $params);
        
        return $resultado['total'];
    }
}
?>
```

**Conceptos de transacciones a explicar:**

1. **¿Cuándo usar transacciones?**
   - Cuando necesitas insertar en múltiples tablas relacionadas
   - Si una operación falla, TODAS deben fallar (atomicidad)
   - Ejemplo: Ruta + Épocas + Fotos deben insertarse juntas

2. **Flujo de transacción:**
   ```
   BEGIN TRANSACTION
   ↓
   INSERT ruta → obtener ID
   ↓
   INSERT épocas (con ID de ruta)
   ↓
   INSERT fotos (con ID de ruta)
   ↓
   ¿Algún error? → ROLLBACK (deshacer todo)
   ↓
   Sin errores → COMMIT (confirmar todo)
   ```

---

#### **Sesión 4.2: Sistema de Comentarios (Tarea 2.6)**

```php
// models/Comentario.php

<?php
require_once '../config/Database.php';

class Comentario {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Añadir comentario
    public function crear($rutaId, $userId, $texto) {
        $sql = "INSERT INTO comentarios (ruta_id, user_id, comentario) 
                VALUES (?, ?, ?)";
        
        $this->db->execute($sql, "iis", [$rutaId, $userId, $texto]);
        return $this->db->lastInsertId();
    }
    
    // Obtener comentarios de una ruta
    public function obtenerPorRuta($rutaId) {
        $sql = "SELECT c.*, u.username, u.foto_perfil
                FROM comentarios c
                INNER JOIN usuarios u ON c.user_id = u.id
                WHERE c.ruta_id = ?
                ORDER BY c.fecha_creacion DESC";
        
        return $this->db->fetchAll($sql, "i", [$rutaId]);
    }
    
    // Eliminar comentario
    public function eliminar($id, $userId) {
        // Verificar que el usuario sea el autor del comentario
        $sql = "DELETE FROM comentarios WHERE id = ? AND user_id = ?";
        $affected = $this->db->execute($sql, "ii", [$id, $userId]);
        
        return $affected > 0;
    }
    
    // Verificar si el usuario puede eliminar (es autor o dueño de la ruta)
    public function puedeEliminar($comentarioId, $userId) {
        $sql = "SELECT c.user_id, r.user_id as ruta_user_id
                FROM comentarios c
                INNER JOIN rutas r ON c.ruta_id = r.id
                WHERE c.id = ?";
        
        $resultado = $this->db->fetchOne($sql, "i", [$comentarioId]);
        
        if (!$resultado) return false;
        
        // Puede eliminar si es el autor del comentario o el dueño de la ruta
        return $resultado['user_id'] == $userId || $resultado['ruta_user_id'] == $userId;
    }
}
?>
```

**Uso en la vista:**

```php
// public/routes/view.php

<?php
session_start();
require_once '../../models/Ruta.php';
require_once '../../models/Comentario.php';

$rutaId = $_GET['id'] ?? 0;
$rutaModel = new Ruta();
$comentarioModel = new Comentario();

// Obtener ruta
$ruta = $rutaModel->obtenerPorId($rutaId);

if (!$ruta) {
    header("Location: list.php");
    exit();
}

// Obtener comentarios
$comentarios = $comentarioModel->obtenerPorRuta($rutaId);

// Procesar nuevo comentario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $texto = trim($_POST['comentario']);
    
    if (!empty($texto)) {
        try {
            $comentarioModel->crear($rutaId, $_SESSION['user_id'], $texto);
            header("Location: view.php?id=$rutaId");
            exit();
        } catch (Exception $e) {
            $error = "Error al añadir comentario: " . $e->getMessage();
        }
    }
}

include '../../includes/header.php';
?>

<!-- Mostrar información de la ruta -->
<div class="ruta-detalle">
    <h1><?= htmlspecialchars($ruta['nombre']) ?></h1>
    <p>Creada por: <?= htmlspecialchars($ruta['username']) ?></p>
    <p>Dificultad: <?= htmlspecialchars($ruta['dificultad']) ?></p>
    <p>Distancia: <?= $ruta['distancia'] ?> km</p>
    <p>Desnivel: <?= $ruta['desnivel'] ?> m</p>
    <p><?= nl2br(htmlspecialchars($ruta['descripcion'])) ?></p>
    
    <!-- Galería de fotos -->
    <?php if (!empty($ruta['fotos'])): ?>
        <div class="galeria">
            <?php foreach ($ruta['fotos'] as $foto): ?>
                <img src="../../uploads/photos/<?= htmlspecialchars($foto['nombre_archivo']) ?>" 
                     alt="<?= htmlspecialchars($foto['titulo'] ?? 'Foto') ?>">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Sección de comentarios -->
<div class="comentarios">
    <h2>Comentarios (<?= count($comentarios) ?>)</h2>
    
    <!-- Formulario para añadir comentario (solo si está logueado) -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <form method="POST" class="form-comentario">
            <textarea name="comentario" rows="3" placeholder="Escribe un comentario..." required></textarea>
            <button type="submit">Enviar comentario</button>
        </form>
    <?php else: ?>
        <p><a href="../../login.php">Inicia sesión</a> para comentar</p>
    <?php endif; ?>
    
    <!-- Lista de comentarios -->
    <?php if (empty($comentarios)): ?>
        <p>No hay comentarios aún. ¡Sé el primero en comentar!</p>
    <?php else: ?>
        <?php foreach ($comentarios as $comentario): ?>
            <div class="comentario">
                <div class="comentario-header">
                    <strong><?= htmlspecialchars($comentario['username']) ?></strong>
                    <span class="fecha"><?= date('d/m/Y H:i', strtotime($comentario['fecha_creacion'])) ?></span>
                </div>
                <div class="comentario-texto">
                    <?= nl2br(htmlspecialchars($comentario['comentario'])) ?>
                </div>
                
                <!-- Botón eliminar (si puede) -->
                <?php if (isset($_SESSION['user_id']) && 
                          $comentarioModel->puedeEliminar($comentario['id'], $_SESSION['user_id'])): ?>
                    <form method="POST" action="../../comments/delete.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $comentario['id'] ?>">
                        <input type="hidden" name="ruta_id" value="<?= $rutaId ?>">
                        <button type="submit" onclick="return confirm('¿Eliminar comentario?')">Eliminar</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>
```

---

#### **Sesión 4.3: Sistema de Likes (Tarea 2.7)**

```php
// models/Like.php

<?php
require_once '../config/Database.php';

class Like {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Alternar like (añadir o quitar)
    public function toggle($rutaId, $userId) {
        // Verificar si ya existe
        if ($this->existe($rutaId, $userId)) {
            return $this->quitar($rutaId, $userId);
        } else {
            return $this->dar($rutaId, $userId);
        }
    }
    
    // Dar like
    private function dar($rutaId, $userId) {
        try {
            $this->db->beginTransaction();
            
            // Insertar en tabla likes
            $sql = "INSERT INTO likes (ruta_id, user_id) VALUES (?, ?)";
            $this->db->execute($sql, "ii", [$rutaId, $userId]);
            
            // Incrementar contador en rutas
            $sql = "UPDATE rutas SET likes = likes + 1 WHERE id = ?";
            $this->db->execute($sql, "i", [$rutaId]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    // Quitar like
    private function quitar($rutaId, $userId) {
        try {
            $this->db->beginTransaction();
            
            // Eliminar de tabla likes
            $sql = "DELETE FROM likes WHERE ruta_id = ? AND user_id = ?";
            $this->db->execute($sql, "ii", [$rutaId, $userId]);
            
            // Decrementar contador en rutas
            $sql = "UPDATE rutas SET likes = likes - 1 WHERE id = ?";
            $this->db->execute($sql, "i", [$rutaId]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    // Verificar si existe like
    public function existe($rutaId, $userId) {
        $sql = "SELECT id FROM likes WHERE ruta_id = ? AND user_id = ?";
        $resultado = $this->db->fetchOne($sql, "ii", [$rutaId, $userId]);
        return $resultado !== null;
    }
    
    // Obtener número de likes de una ruta
    public function contar($rutaId) {
        $sql = "SELECT COUNT(*) as total FROM likes WHERE ruta_id = ?";
        $resultado = $this->db->fetchOne($sql, "i", [$rutaId]);
        return $resultado['total'];
    }
}
?>
```

```php
// public/routes/like.php

<?php
session_start();
require_once '../../models/Like.php';

// Verificar que esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$rutaId = $_POST['ruta_id'] ?? 0;
$userId = $_SESSION['user_id'];

if ($rutaId > 0) {
    $likeModel = new Like();
    $likeModel->toggle($rutaId, $userId);
}

// Redireccionar de vuelta
$referer = $_SERVER['HTTP_REFERER'] ?? 'list.php';
header("Location: $referer");
exit();
?>
```

**Añadir botón en vista de ruta:**

```php
<?php
// En view.php, después de cargar la ruta
$likeModel = new Like();
$tienelike = isset($_SESSION['user_id']) ? 
    $likeModel->existe($rutaId, $_SESSION['user_id']) : false;
?>

<div class="likes">
    <span>❤️ <?= $ruta['likes'] ?> likes</span>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <form method="POST" action="like.php" style="display:inline;">
            <input type="hidden" name="ruta_id" value="<?= $rutaId ?>">
            <button type="submit">
                <?= $tienelike ? '💔 Quitar Like' : '❤️ Me gusta' ?>
            </button>
        </form>
    <?php endif; ?>
</div>
```

---

### **Etapa 5: Búsqueda Avanzada y Optimización**
**Duración: 2-3 sesiones**

#### **Sesión 5.1: Búsqueda con Consultas Dinámicas (Tarea 2.8)**

```php
// models/Busqueda.php

<?php
require_once '../config/Database.php';

class Busqueda {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Búsqueda avanzada
    public function buscar($criterios, $pagina = 1, $porPagina = 10) {
        $where = ["r.activa = 1"];
        $tipos = "";
        $params = [];
        
        // Búsqueda por texto (nombre o descripción)
        if (!empty($criterios['texto'])) {
            $where[] = "(r.nombre LIKE ? OR r.descripcion LIKE ?)";
            $busqueda = "%" . $criterios['texto'] . "%";
            $tipos .= "ss";
            $params[] = $busqueda;
            $params[] = $busqueda;
        }
        
        // Filtro por dificultad
        if (!empty($criterios['dificultad'])) {
            $where[] = "r.dificultad = ?";
            $tipos .= "s";
            $params[] = $criterios['dificultad'];
        }
        
        // Filtro por provincia
        if (!empty($criterios['provincia'])) {
            $where[] = "r.provincia = ?";
            $tipos .= "s";
            $params[] = $criterios['provincia'];
        }
        
        // Rango de distancia
        if (!empty($criterios['distancia_min'])) {
            $where[] = "r.distancia >= ?";
            $tipos .= "d";
            $params[] = $criterios['distancia_min'];
        }
        if (!empty($criterios['distancia_max'])) {
            $where[] = "r.distancia <= ?";
            $tipos .= "d";
            $params[] = $criterios['distancia_max'];
        }
        
        // Rango de desnivel
        if (!empty($criterios['desnivel_min'])) {
            $where[] = "r.desnivel >= ?";
            $tipos .= "i";
            $params[] = $criterios['desnivel_min'];
        }
        if (!empty($criterios['desnivel_max'])) {
            $where[] = "r.desnivel <= ?";
            $tipos .= "i";
            $params[] = $criterios['desnivel_max'];
        }
        
        // Nivel técnico máximo
        if (!empty($criterios['nivel_tecnico'])) {
            $where[] = "r.nivel_tecnico <= ?";
            $tipos .= "i";
            $params[] = $criterios['nivel_tecnico'];
        }
        
        // Nivel físico máximo
        if (!empty($criterios['nivel_fisico'])) {
            $where[] = "r.nivel_fisico <= ?";
            $tipos .= "i";
            $params[] = $criterios['nivel_fisico'];
        }
        
        // Construir SQL
        $whereSQL = implode(" AND ", $where);
        
        // Ordenamiento
        $orderBy = "r.fecha_creacion DESC";
        if (!empty($criterios['orden'])) {
            switch ($criterios['orden']) {
                case 'mas_visitadas':
                    $orderBy = "r.visitas DESC";
                    break;
                case 'mejor_valoradas':
                    $orderBy = "r.likes DESC";
                    break;
                case 'mas_recientes':
                    $orderBy = "r.fecha_creacion DESC";
                    break;
            }
        }
        
        // Paginación
        $offset = ($pagina - 1) * $porPagina;
        
        $sql = "SELECT r.*, u.username,
                (SELECT nombre_archivo FROM fotografias WHERE ruta_id = r.id ORDER BY orden LIMIT 1) as foto_portada
                FROM rutas r
                INNER JOIN usuarios u ON r.user_id = u.id
                WHERE $whereSQL
                ORDER BY $orderBy
                LIMIT ? OFFSET ?";
        
        // Añadir límite y offset
        $tipos .= "ii";
        $params[] = $porPagina;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $tipos, $params);
    }
    
    // Contar resultados (para paginación)
    public function contarResultados($criterios) {
        $where = ["activa = 1"];
        $tipos = "";
        $params = [];
        
        // Repetir la lógica de filtros (sin paginación)
        if (!empty($criterios['texto'])) {
            $where[] = "(nombre LIKE ? OR descripcion LIKE ?)";
            $busqueda = "%" . $criterios['texto'] . "%";
            $tipos .= "ss";
            $params[] = $busqueda;
            $params[] = $busqueda;
        }
        
        // ... resto de filtros igual que en buscar()
        
        $whereSQL = implode(" AND ", $where);
        $sql = "SELECT COUNT(*) as total FROM rutas WHERE $whereSQL";
        
        $resultado = empty($tipos) ? 
            $this->db->fetchOne($sql) : 
            $this->db->fetchOne($sql, $tipos, $params);
        
        return $resultado['total'];
    }
}
?>
```

```php
// public/search/index.php

<?php
session_start();
require_once '../../models/Busqueda.php';

$busquedaModel = new Busqueda();

// Obtener criterios de búsqueda
$criterios = [
    'texto' => $_GET['texto'] ?? '',
    'dificultad' => $_GET['dificultad'] ?? '',
    'provincia' => $_GET['provincia'] ?? '',
    'distancia_min' => $_GET['distancia_min'] ?? '',
    'distancia_max' => $_GET['distancia_max'] ?? '',
    'desnivel_min' => $_GET['desnivel_min'] ?? '',
    'desnivel_max' => $_GET['desnivel_max'] ?? '',
    'nivel_tecnico' => $_GET['nivel_tecnico'] ?? '',
    'nivel_fisico' => $_GET['nivel_fisico'] ?? '',
    'orden' => $_GET['orden'] ?? 'mas_recientes'
];

$pagina = $_GET['pagina'] ?? 1;
$porPagina = 10;

// Realizar búsqueda
$resultados = $busquedaModel->buscar($criterios, $pagina, $porPagina);
$totalResultados = $busquedaModel->contarResultados($criterios);
$totalPaginas = ceil($totalResultados / $porPagina);

include '../../includes/header.php';
?>

<h1>Búsqueda Avanzada de Rutas</h1>

<!-- Formulario de búsqueda -->
<form method="GET" class="form-busqueda">
    <div class="form-group">
        <label>Buscar en nombre o descripción:</label>
        <input type="text" name="texto" value="<?= htmlspecialchars($criterios['texto']) ?>" 
               placeholder="Ej: Picos de Europa">
    </div>
    
    <div class="form-group">
        <label>Dificultad:</label>
        <select name="dificultad">
            <option value="">Todas</option>
            <option value="Fácil" <?= $criterios['dificultad'] == 'Fácil' ? 'selected' : '' ?>>Fácil</option>
            <option value="Moderada" <?= $criterios['dificultad'] == 'Moderada' ? 'selected' : '' ?>>Moderada</option>
            <option value="Difícil" <?= $criterios['dificultad'] == 'Difícil' ? 'selected' : '' ?>>Difícil</option>
            <option value="Muy difícil" <?= $criterios['dificultad'] == 'Muy difícil' ? 'selected' : '' ?>>Muy difícil</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>Provincia:</label>
        <select name="provincia">
            <option value="">Todas</option>
            <!-- Aquí irían todas las provincias -->
            <option value="Asturias" <?= $criterios['provincia'] == 'Asturias' ? 'selected' : '' ?>>Asturias</option>
            <option value="León" <?= $criterios['provincia'] == 'León' ? 'selected' : '' ?>>León</option>
            <!-- ... -->
        </select>
    </div>
    
    <div class="form-group">
        <label>Distancia (km):</label>
        <input type="number" name="distancia_min" value="<?= htmlspecialchars($criterios['distancia_min']) ?>" 
               placeholder="Mínimo" step="0.1" min="0">
        <input type="number" name="distancia_max" value="<?= htmlspecialchars($criterios['distancia_max']) ?>" 
               placeholder="Máximo" step="0.1" min="0">
    </div>
    
    <div class="form-group">
        <label>Desnivel (m):</label>
        <input type="number" name="desnivel_min" value="<?= htmlspecialchars($criterios['desnivel_min']) ?>" 
               placeholder="Mínimo" min="0">
        <input type="number" name="desnivel_max" value="<?= htmlspecialchars($criterios['desnivel_max']) ?>" 
               placeholder="Máximo" min="0">
    </div>
    
    <div class="form-group">
        <label>Ordenar por:</label>
        <select name="orden">
            <option value="mas_recientes" <?= $criterios['orden'] == 'mas_recientes' ? 'selected' : '' ?>>Más recientes</option>
            <option value="mas_visitadas" <?= $criterios['orden'] == 'mas_visitadas' ? 'selected' : '' ?>>Más visitadas</option>
            <option value="mejor_valoradas" <?= $criterios['orden'] == 'mejor_valoradas' ? 'selected' : '' ?>>Mejor valoradas</option>
        </select>
    </div>
    
    <button type="submit">Buscar</button>
    <a href="index.php">Limpiar filtros</a>
</form>

<!-- Resultados -->
<h2>Resultados: <?= $totalResultados ?> rutas encontradas</h2>

<?php if (empty($resultados)): ?>
    <p>No se encontraron rutas con esos criterios.</p>
<?php else: ?>
    <div class="resultados-grid">
        <?php foreach ($resultados as $ruta): ?>
            <div class="ruta-card">
                <?php if ($ruta['foto_portada']): ?>
                    <img src="../../uploads/photos/<?= htmlspecialchars($ruta['foto_portada']) ?>" 
                         alt="<?= htmlspecialchars($ruta['nombre']) ?>">
                <?php endif; ?>
                
                <h3><a href="../routes/view.php?id=<?= $ruta['id'] ?>"><?= htmlspecialchars($ruta['nombre']) ?></a></h3>
                <p><?= htmlspecialchars($ruta['dificultad']) ?> | <?= $ruta['distancia'] ?> km | <?= $ruta['desnivel'] ?> m</p>
                <p>Por: <?= htmlspecialchars($ruta['username']) ?></p>
                <p>❤️ <?= $ruta['likes'] ?> | 👁️ <?= $ruta['visitas'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Paginación -->
    <?php if ($totalPaginas > 1): ?>
        <div class="paginacion">
            <?php
            // Construir URL con parámetros
            $queryString = http_build_query(array_filter($criterios));
            ?>
            
            <?php if ($pagina > 1): ?>
                <a href="?<?= $queryString ?>&pagina=<?= $pagina - 1 ?>">« Anterior</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <?php if ($i == $pagina): ?>
                    <strong><?= $i ?></strong>
                <?php else: ?>
                    <a href="?<?= $queryString ?>&pagina=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($pagina < $totalPaginas): ?>
                <a href="?<?= $queryString ?>&pagina=<?= $pagina + 1 ?>">Siguiente »</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
```

**Conceptos a explicar:**
- Construcción dinámica de WHERE
- Operador LIKE para búsqueda de texto
- Manejo de número variable de parámetros
- `http_build_query()` para mantener filtros en paginación

---

#### **Sesión 5.2: Funciones Auxiliares y Helpers**

```php
// includes/db_functions.php

<?php
/**
 * Funciones auxiliares para operaciones comunes de base de datos
 */

// Escapar HTML para prevenir XSS
function escaparHTML($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

// Generar HTML de paginación
function generarPaginacion($paginaActual, $totalPaginas, $url) {
    $html = '<div class="paginacion">';
    
    // Botón anterior
    if ($paginaActual > 1) {
        $html .= '<a href="' . $url . '&pagina=' . ($paginaActual - 1) . '">« Anterior</a>';
    }
    
    // Números de página
    for ($i = 1; $i <= $totalPaginas; $i++) {
        if ($i == $paginaActual) {
            $html .= '<strong>' . $i . '</strong>';
        } else {
            $html .= '<a href="' . $url . '&pagina=' . $i . '">' . $i . '</a>';
        }
    }
    
    // Botón siguiente
    if ($paginaActual < $totalPaginas) {
        $html .= '<a href="' . $url . '&pagina=' . ($paginaActual + 1) . '">Siguiente »</a>';
    }
    
    $html .= '</div>';
    return $html;
}

// Formatear fecha
function formatearFecha($fecha) {
    return date('d/m/Y H:i', strtotime($fecha));
}

// Truncar texto
function truncar($texto, $longitud = 100) {
    if (strlen($texto) > $longitud) {
        return substr($texto, 0, $longitud) . '...';
    }
    return $texto;
}

// Obtener badge de dificultad con color
function badgeDificultad($dificultad) {
    $colores = [
        'Fácil' => 'green',
        'Moderada' => 'blue',
        'Difícil' => 'orange',
        'Muy difícil' => 'red'
    ];
    
    $color = $colores[$dificultad] ?? 'gray';
    return '<span class="badge badge-' . $color . '">' . escaparHTML($dificultad) . '</span>';
}

// Subir archivo con validación
function subirArchivo($archivo, $directorioDestino, $tiposPermitidos = ['jpg', 'jpeg', 'png'], $pesoMaximo = 5242880) {
    // Verificar errores
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Error al subir el archivo");
    }
    
    // Verificar tamaño
    if ($archivo['size'] > $pesoMaximo) {
        throw new Exception("El archivo es demasiado grande. Máximo: " . ($pesoMaximo / 1024 / 1024) . "MB");
    }
    
    // Verificar tipo MIME real
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo,
