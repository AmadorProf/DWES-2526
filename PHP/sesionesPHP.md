# Cookies y Sesiones en PHP

## 1. Cookies

### ¿Qué son las Cookies?

Las cookies son pequeños archivos de texto que se almacenan en el navegador del cliente. Se utilizan para guardar información entre diferentes peticiones HTTP, permitiendo mantener datos del usuario a través de múltiples visitas.

### Características de las Cookies

- Se almacenan en el lado del cliente (navegador)
- Tienen un tamaño limitado (aproximadamente 4KB)
- Pueden tener fecha de expiración
- Son visibles y modificables por el usuario
- Se envían automáticamente en cada petición al servidor

### Crear una Cookie

```php
setcookie(nombre, valor, expiracion, ruta, dominio, seguro, httponly);
```

**Parámetros:**
- `nombre`: nombre de la cookie (obligatorio)
- `valor`: valor que almacena (obligatorio)
- `expiracion`: timestamp de expiración (opcional)
- `ruta`: ruta del servidor donde está disponible (opcional, por defecto `/`)
- `dominio`: dominio donde está disponible (opcional)
- `seguro`: solo se envía por HTTPS si es `true` (opcional)
- `httponly`: no accesible desde JavaScript si es `true` (opcional)

### Ejemplos de Uso

```php
// Cookie que expira en 1 hora
setcookie("usuario", "Juan", time() + 3600);

// Cookie que expira en 30 días
setcookie("preferencia", "modo_oscuro", time() + (30 * 24 * 3600));

// Cookie de sesión (expira al cerrar el navegador)
setcookie("temporal", "valor", 0);

// Cookie segura y con httponly
setcookie("token", "abc123", time() + 3600, "/", "", true, true);
```

### Leer Cookies

```php
// Verificar si existe una cookie
if(isset($_COOKIE["usuario"])) {
    echo "Bienvenido " . $_COOKIE["usuario"];
} else {
    echo "Usuario no identificado";
}

// Leer el valor directamente
$nombre_usuario = $_COOKIE["usuario"] ?? "Invitado";
```

### Modificar una Cookie

Para modificar una cookie, simplemente vuelve a usar `setcookie()` con el mismo nombre:

```php
setcookie("usuario", "Pedro", time() + 3600);
```

### Eliminar una Cookie

Para eliminar una cookie, establece su fecha de expiración en el pasado:

```php
setcookie("usuario", "", time() - 3600);
// o también:
setcookie("usuario", "", 1);
```

### Consideraciones Importantes

⚠️ **IMPORTANTE:** `setcookie()` debe llamarse ANTES de cualquier salida HTML:

```php
<?php
// ✓ CORRECTO
setcookie("usuario", "Juan", time() + 3600);
?>
<!DOCTYPE html>
<html>...

<?php
// ✗ INCORRECTO
?>
<!DOCTYPE html>
<html>
<?php
setcookie("usuario", "Juan", time() + 3600); // Error!
```

### Seguridad con Cookies

```php
// Buenas prácticas de seguridad
setcookie(
    "sesion_token",
    $token_seguro,
    time() + 3600,
    "/",
    "",
    true,      // Solo HTTPS
    true       // No accesible desde JavaScript
);

// Validar y sanitizar siempre los datos de cookies
$usuario = filter_var($_COOKIE["usuario"] ?? "", FILTER_SANITIZE_STRING);
```

---

## 2. Sesiones

### ¿Qué son las Sesiones?

Las sesiones permiten almacenar información del usuario en el servidor entre diferentes peticiones HTTP. Cada usuario recibe un identificador único de sesión (session ID) que se almacena en una cookie en el cliente.

### Características de las Sesiones

- Los datos se almacenan en el servidor (más seguro)
- Mayor capacidad de almacenamiento que las cookies
- Expiran automáticamente tras un periodo de inactividad
- No son modificables directamente por el usuario
- Solo se envía el ID de sesión al cliente

### Iniciar una Sesión

```php
session_start();
```

**Debe llamarse al inicio del script, antes de cualquier salida HTML**

### Almacenar Datos en la Sesión

```php
session_start();

$_SESSION["usuario"] = "Juan";
$_SESSION["email"] = "juan@example.com";
$_SESSION["rol"] = "admin";
$_SESSION["carrito"] = ["producto1", "producto2"];
```

### Leer Datos de la Sesión

```php
session_start();

if(isset($_SESSION["usuario"])) {
    echo "Bienvenido " . $_SESSION["usuario"];
} else {
    echo "No has iniciado sesión";
}

// Con operador de fusión null
$usuario = $_SESSION["usuario"] ?? "Invitado";
```

### Modificar Datos de la Sesión

```php
session_start();

$_SESSION["contador"] = $_SESSION["contador"] ?? 0;
$_SESSION["contador"]++;
```

### Eliminar Variables de Sesión

```php
session_start();

// Eliminar una variable específica
unset($_SESSION["usuario"]);

// Eliminar todas las variables de sesión
$_SESSION = [];
```

### Destruir una Sesión Completamente

```php
session_start();

// 1. Eliminar todas las variables de sesión
$_SESSION = [];

// 2. Eliminar la cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// 3. Destruir la sesión en el servidor
session_destroy();
```

### Ejemplo Completo: Sistema de Login

**login.php**
```php
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];
    
    // Validar credenciales (ejemplo simplificado)
    if ($usuario == "admin" && $password == "1234") {
        $_SESSION["usuario"] = $usuario;
        $_SESSION["login_time"] = time();
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Credenciales incorrectas";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required><br>
        <input type="password" name="password" placeholder="Contraseña" required><br>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
```

**dashboard.php**
```php
<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Bienvenido <?php echo htmlspecialchars($_SESSION["usuario"]); ?></h1>
    <p>Has iniciado sesión desde: <?php echo date("H:i:s", $_SESSION["login_time"]); ?></p>
    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>
```

**logout.php**
```php
<?php
session_start();
$_SESSION = [];
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}
session_destroy();
header("Location: login.php");
exit();
?>
```

### Configuración de Sesiones

```php
// Cambiar el tiempo de vida de la sesión (en segundos)
ini_set('session.gc_maxlifetime', 3600); // 1 hora

// Establecer tiempo de vida de la cookie de sesión
session_set_cookie_params(3600);

// Regenerar el ID de sesión (seguridad)
session_regenerate_id(true);
```

### Seguridad en Sesiones

```php
// Configuración segura de sesiones
ini_set('session.cookie_httponly', 1);  // No accesible desde JavaScript
ini_set('session.use_only_cookies', 1);  // Solo usar cookies
ini_set('session.cookie_secure', 1);     // Solo HTTPS (en producción)

// Regenerar ID tras login (prevenir session fixation)
session_start();
if (isset($_POST["usuario"])) {
    // ... validar credenciales ...
    session_regenerate_id(true);
    $_SESSION["usuario"] = $usuario;
}

// Validar sesión con datos adicionales
$_SESSION["user_agent"] = $_SERVER["HTTP_USER_AGENT"];
$_SESSION["ip"] = $_SERVER["REMOTE_ADDR"];
```

---

## 3. Cookies vs Sesiones

| Característica | Cookies | Sesiones |
|----------------|---------|----------|
| **Almacenamiento** | Cliente (navegador) | Servidor |
| **Capacidad** | ~4KB | Limitado por servidor |
| **Seguridad** | Menos seguro | Más seguro |
| **Expiración** | Manual | Automática por inactividad |
| **Visibilidad** | Usuario puede ver/modificar | Usuario no puede acceder |
| **Rendimiento** | Se envía en cada petición | Solo se envía ID |
| **Persistencia** | Puede ser permanente | Temporal |

### ¿Cuándo Usar Cada Una?

**Usar Cookies para:**
- Preferencias de idioma, tema, etc.
- Recordar usuario (no contraseñas)
- Seguimiento de analíticas
- Datos no sensibles que deben persistir

**Usar Sesiones para:**
- Autenticación de usuarios
- Datos sensibles
- Carritos de compra
- Información temporal durante la navegación
- Datos que no deben ser manipulables

### Uso Combinado

```php
<?php
session_start();

// Cookie para recordar usuario
if (isset($_COOKIE["recordar_usuario"])) {
    $usuario_guardado = $_COOKIE["recordar_usuario"];
}

// Sesión para autenticación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $recordar = isset($_POST["recordar"]);
    
    // Validar...
    $_SESSION["usuario"] = $usuario;
    $_SESSION["autenticado"] = true;
    
    // Si marca "recordar", guardar en cookie
    if ($recordar) {
        setcookie("recordar_usuario", $usuario, time() + (30 * 24 * 3600));
    }
}
?>
```

---

## 4. Problemas Comunes y Soluciones

### "Headers already sent"

**Problema:** Salida HTML antes de `setcookie()` o `session_start()`

```php
// ✗ INCORRECTO
echo "Hola";
session_start(); // Error!

// ✓ CORRECTO
session_start();
echo "Hola";
```

### Sesión No Persiste

**Solución:** Verificar que `session_start()` esté en todas las páginas

```php
// En cada página que use sesiones
<?php
session_start();
?>
```

### Cookie No Se Guarda

**Posibles causas:**
- Cookies deshabilitadas en el navegador
- Fecha de expiración incorrecta
- Ruta o dominio incorrectos
- Salida previa al `setcookie()`

### Datos de Sesión Se Pierden

**Soluciones:**
- Verificar permisos del directorio de sesiones
- Aumentar `session.gc_maxlifetime`
- Asegurar que el servidor no esté eliminando sesiones prematuramente

---

## 5. Ejercicios Prácticos

### Ejercicio 1: Contador de Visitas

Crea una página que cuente cuántas veces ha visitado un usuario usando una cookie.

### Ejercicio 2: Sistema de Login Completo

Implementa un sistema con:
- Página de login
- Página protegida
- Opción "Recordarme"
- Cierre de sesión seguro

### Ejercicio 3: Carrito de Compras

Crea un carrito de compras usando sesiones que permita:
- Agregar productos
- Ver productos
- Eliminar productos
- Calcular total

### Ejercicio 4: Preferencias de Usuario

Usa cookies para guardar:
- Idioma preferido
- Tema (claro/oscuro)
- Tamaño de fuente

---
