# Desarrollo Web en Entorno Servidor
## Conexión a Bases de Datos con PHP y MySQL

---

## 1. Introducción al Acceso a Bases de Datos

### 1.1 Librerías disponibles en PHP

PHP proporciona varias opciones para conectarse a bases de datos MySQL:

#### **MySQL (Obsoleta)**
- Librería original de PHP para MySQL
- **Deprecada** desde PHP 5.5
- **Eliminada** completamente desde PHP 7.0
- No se recomienda su uso

#### **MySQLi (MySQL Improved)**
- Librería mejorada y moderna
- Dos versiones disponibles:
  - **Procedimental**: Funciones tradicionales (la que usaremos)
  - **Orientada a objetos**: Uso de clases y métodos
- Específica para MySQL
- Soporta prepared statements (consultas preparadas)
- Mejor rendimiento y seguridad

#### **PDO (PHP Data Objects)**
- Librería orientada a objetos
- **Ventaja principal**: Compatible con múltiples gestores de bases de datos:
  - MySQL
  - PostgreSQL
  - SQLite
  - Oracle
  - SQL Server
- Portabilidad entre diferentes SGBD
- Soporta prepared statements

---

## 2. Flujo de Trabajo con MySQLi

### 2.1 Pasos fundamentales

Todo proceso de interacción con una base de datos sigue estos pasos:

```
1. CONEXIÓN → 2. EJECUCIÓN → 3. PROCESAMIENTO → 4. CIERRE
```

1. **Conectar** con el servidor y seleccionar la base de datos
2. **Ejecutar** instrucciones SQL
3. **Procesar** los resultados (si es necesario)
4. **Cerrar** la conexión
5. **Controlar errores** en cada paso

---

## 3. Funciones Principales de MySQLi

### 3.1 Conexión al Servidor

#### `mysqli_connect()`
```php
$conexion = mysqli_connect(servidor, usuario, contraseña, [nombreBD]);
```

**Parámetros:**
- `servidor`: Dirección del servidor (ej: "localhost", "127.0.0.1")
- `usuario`: Nombre de usuario de MySQL (ej: "root")
- `contraseña`: Contraseña del usuario
- `nombreBD`: (Opcional) Nombre de la base de datos

**Retorno:**
- Si tiene éxito: objeto de conexión
- Si falla: `false`

**Ejemplos:**

```php
// Ejemplo 1: Conexión con base de datos
$conexion = mysqli_connect("localhost", "usuprueba", "12345", "bdprueba");

if ($conexion) {
    echo "Conexión exitosa";
    // ... realizar operaciones ...
    mysqli_close($conexion);
} else {
    echo "Error de conexión: " . mysqli_error($conexion);
    echo "Número de error: " . mysqli_errno($conexion);
}
```

```php
// Ejemplo 2: Conexión sin base de datos (die si falla)
$conexion = mysqli_connect("localhost", "root", "")
    or die("No se puede conectar con el servidor");
```

---

### 3.2 Selección de Base de Datos

#### `mysqli_select_db()`
```php
mysqli_select_db($conexion, nombreBaseDatos);
```

**Uso:** Solo necesario si no se especificó la BD en `mysqli_connect()`

**Ejemplo:**
```php
$conexion = mysqli_connect("localhost", "root", "")
    or die("No se puede conectar con el servidor");

mysqli_select_db($conexion, "bdprueba")
    or die("No se puede seleccionar la base de datos");
```

---

### 3.3 Ejecución de Consultas SQL

#### `mysqli_query()`
```php
$resultado = mysqli_query($conexion, sentenciaSQL);
```

**Tipos de sentencias SQL soportadas:**
- **DDL (Data Definition Language)**:
  - `CREATE DATABASE` / `DROP DATABASE`
  - `CREATE TABLE` / `DROP TABLE` / `ALTER TABLE`
  
- **DML (Data Manipulation Language)**:
  - `INSERT` - Insertar registros
  - `UPDATE` - Modificar registros
  - `DELETE` - Eliminar registros
  
- **DQL (Data Query Language)**:
  - `SELECT` - Consultar datos
  - `SHOW` / `DESCRIBE` / `EXPLAIN`

**Retorno:**
- Para INSERT, UPDATE, DELETE, CREATE, DROP: `true` si tiene éxito, `false` si falla
- Para SELECT y similares: objeto `result set` o `false` si falla

---

### 3.4 Procesamiento de Resultados

#### `mysqli_num_rows()`
```php
$numFilas = mysqli_num_rows($resultado);
```
Devuelve el número de filas del resultado de una consulta SELECT.

---

#### `mysqli_fetch_assoc()`
```php
$fila = mysqli_fetch_assoc($resultado);
```

- Obtiene la **siguiente fila** del resultado como **array asociativo**
- Las claves son los nombres de las columnas
- Devuelve `false` cuando no hay más filas

**Ejemplo:**
```php
$fila = mysqli_fetch_assoc($resultado);
// $fila = ["id" => 1, "nombre" => "Juan", "email" => "juan@email.com"]
echo $fila['nombre']; // Juan
```

---

#### `mysqli_fetch_row()`
```php
$fila = mysqli_fetch_row($resultado);
```

- Obtiene la siguiente fila como **array numérico (escalar)**
- Las claves son índices numéricos (0, 1, 2...)
- Devuelve `false` cuando no hay más filas

**Ejemplo:**
```php
$fila = mysqli_fetch_row($resultado);
// $fila = [1, "Juan", "juan@email.com"]
echo $fila[1]; // Juan
```

---

#### `mysqli_fetch_array()`
```php
$fila = mysqli_fetch_array($resultado, [tipo]);
```

**Tipos de array:**
- `MYSQLI_ASSOC`: Array asociativo (como `mysqli_fetch_assoc`)
- `MYSQLI_NUM`: Array numérico (como `mysqli_fetch_row`)
- `MYSQLI_BOTH`: Ambos tipos (por defecto)

---

### 3.5 Control de Errores

#### `mysqli_error()`
```php
$mensajeError = mysqli_error($conexion);
```
Devuelve la descripción del último error ocurrido.

#### `mysqli_errno()`
```php
$numeroError = mysqli_errno($conexion);
```
Devuelve el código numérico del último error (0 = sin error).

**Ejemplo de uso:**
```php
if (!mysqli_query($conexion, $sql)) {
    echo "Error: " . mysqli_error($conexion);
    echo " (Código: " . mysqli_errno($conexion) . ")";
}
```

---

### 3.6 Cierre de Conexión

#### `mysqli_close()`
```php
mysqli_close($conexion);
```

Cierra la conexión con el servidor de base de datos. Aunque PHP cierra automáticamente las conexiones al finalizar el script, es buena práctica cerrarlas manualmente.

---

## 4. Ejemplos Prácticos

### 4.1 Crear una Base de Datos

```php
<?php
// Establecer conexión
$conexion = mysqli_connect("localhost", "root", "")
    or die("No se puede conectar con el servidor");

// Preparar sentencia SQL
$sql = "CREATE DATABASE IF NOT EXISTS practicas";

// Ejecutar y verificar
if (mysqli_query($conexion, $sql)) {
    echo "<h2>Base de datos 'practicas' creada con éxito</h2>";
} else {
    echo "<h2>Error al crear la base de datos</h2>";
    echo "Detalle: " . mysqli_error($conexion);
}

// Cerrar conexión
mysqli_close($conexion);
?>
```

---

### 4.2 Crear una Tabla

```php
<?php
// Conectar y seleccionar base de datos
$conexion = mysqli_connect("localhost", "root", "")
    or die("No se puede conectar");

mysqli_select_db($conexion, "practicas")
    or die("No se puede seleccionar la base de datos");

// Preparar sentencia CREATE TABLE
$crear = "CREATE TABLE tabla1 (
    id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    nif CHAR(9) NOT NULL DEFAULT '',
    nombre VARCHAR(40) NOT NULL DEFAULT '',
    PRIMARY KEY (nif),
    UNIQUE auto (id)
)";

// Ejecutar
if (mysqli_query($conexion, $crear)) {
    echo "Tabla 'tabla1' creada con éxito";
} else {
    echo "Error al crear la tabla: " . mysqli_error($conexion);
}

mysqli_close($conexion);
?>
```

---

### 4.3 Insertar Registros

```php
<?php
$conexion = mysqli_connect("localhost", "root", "", "practicas")
    or die("Error de conexión");

// Insertar primer registro
$insertar = "INSERT INTO tabla1 VALUES (1, '16793456A', 'Juan Garcés Ramírez')";

if (mysqli_query($conexion, $insertar)) {
    echo "Primera inserción realizada con éxito<br>";
} else {
    echo "Error en la inserción: " . mysqli_error($conexion);
}

// Insertar segundo registro
$insertar2 = "INSERT INTO tabla1 (nif, nombre) 
              VALUES ('23456789B', 'María López García')";

if (mysqli_query($conexion, $insertar2)) {
    echo "Segunda inserción realizada con éxito";
}

mysqli_close($conexion);
?>
```

---

### 4.4 Actualizar Registros

```php
<?php
$conexion = mysqli_connect("localhost", "root", "", "practicas")
    or die("Error de conexión");

// Modificar un registro
$modificar = "UPDATE tabla1 
              SET nombre = 'Juan Garcés Marín' 
              WHERE nif = '16793456A'";

if (mysqli_query($conexion, $modificar)) {
    echo "Modificación realizada con éxito";
} else {
    echo "Error en la modificación: " . mysqli_error($conexion);
}

mysqli_close($conexion);
?>
```

---

### 4.5 Eliminar Datos

```php
<?php
$conexion = mysqli_connect("localhost", "root", "", "practicas")
    or die("Error de conexión");

// Borrar un registro
mysqli_query($conexion, "DELETE FROM tabla1 WHERE id = 1");

// Borrar una tabla
mysqli_query($conexion, "DROP TABLE tabla1");

// Borrar una base de datos
mysqli_query($conexion, "DROP DATABASE practicas");

mysqli_close($conexion);
?>
```

---

### 4.6 Consultar Datos (SELECT)

#### Método 1: Usando `mysqli_fetch_array()`

```php
<?php
// Conectar
$conexion = mysqli_connect("localhost", "root", "")
    or die("No se puede conectar con el servidor");

mysqli_select_db($conexion, "lindavista")
    or die("No se puede seleccionar la base de datos");

// Preparar y ejecutar consulta
$instruccion = "SELECT * FROM noticias ORDER BY fecha DESC";
$resulconsulta = mysqli_query($conexion, $instruccion)
    or die("Fallo en la consulta");

// Obtener número de filas
$nfilas = mysqli_num_rows($resulconsulta);

if ($nfilas > 0) {
    // Crear tabla HTML
    echo "<table border='1'>";
    echo "<tr>";
    echo "<th>Título</th><th>Texto</th>";
    echo "<th>Categoría</th><th>Fecha</th>";
    echo "</tr>";
    
    // Iterar sobre los resultados
    for ($i = 0; $i < $nfilas; $i++) {
        $resultado = mysqli_fetch_array($resulconsulta);
        
        echo "<tr>";
        echo "<td>" . $resultado['titulo'] . "</td>";
        echo "<td>" . $resultado['texto'] . "</td>";
        echo "<td>" . $resultado['categoria'] . "</td>";
        echo "<td>" . $resultado['fecha'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No hay noticias disponibles";
}

// Cerrar conexión
mysqli_close($conexion);
?>
```

---

#### Método 2: Usando bucle `foreach` anidado

```php
<?php
$conexion = mysqli_connect("localhost", "root", "")
    or die("No se puede conectar");

mysqli_select_db($conexion, "lindavista")
    or die("No se puede seleccionar la base de datos");

$instruccion = "SELECT * FROM noticias ORDER BY fecha DESC";
$resulconsulta = mysqli_query($conexion, $instruccion)
    or die("Fallo en la consulta");

$nfilas = mysqli_num_rows($resulconsulta);

if ($nfilas > 0) {
    echo "<table border='1'>";
    echo "<tr>";
    echo "<th>Título</th><th>Texto</th>";
    echo "<th>Categoría</th><th>Fecha</th>";
    echo "</tr>";
    
    // Usar mysqli_fetch_row para array numérico
    for ($i = 0; $i < $nfilas; $i++) {
        $resultado = mysqli_fetch_row($resulconsulta);
        // O también: mysqli_fetch_assoc($resulconsulta)
        
        echo "<tr>";
        // Iterar sobre cada campo del registro
        foreach ($resultado as $valor) {
            echo "<td>" . $valor . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No hay noticias disponibles";
}

mysqli_close($conexion);
?>
```

---

#### Método 3: Usando bucle `while`

```php
<?php
$conexion = mysqli_connect("localhost", "root", "", "lindavista")
    or die("Error de conexión");

$sql = "SELECT * FROM noticias ORDER BY fecha DESC";
$resultado = mysqli_query($conexion, $sql);

if (mysqli_num_rows($resultado) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Título</th><th>Texto</th><th>Categoría</th><th>Fecha</th></tr>";
    
    // while devuelve false cuando no hay más filas
    while ($fila = mysqli_fetch_assoc($resultado)) {
        echo "<tr>";
        echo "<td>" . $fila['titulo'] . "</td>";
        echo "<td>" . $fila['texto'] . "</td>";
        echo "<td>" . $fila['categoria'] . "</td>";
        echo "<td>" . $fila['fecha'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No hay noticias disponibles";
}

mysqli_close($conexion);
?>
```

---

### 4.7 Listar Bases de Datos Existentes

```php
<?php
// Conectar sin seleccionar BD
$conexion = mysqli_connect("localhost", "root", "")
    or die("Error de conexión");

// Obtener listado de bases de datos
$resultado = mysqli_query($conexion, "SHOW DATABASES");

if ($resultado) {
    $numero = mysqli_num_rows($resultado);
    echo "<h3>Bases de datos disponibles ($numero):</h3>";
    
    while ($nom = mysqli_fetch_row($resultado)) {
        echo "<br>" . $nom[0] . "<br>";
    }
} else {
    echo "Error al obtener bases de datos";
}

mysqli_close($conexion);
?>
```

---

### 4.8 Consultar Estructura de una Tabla

```php
<?php
// Conectar y seleccionar BD
$conexion = mysqli_connect("localhost", "root", "", "practicas")
    or die("Error de conexión");

// Obtener estructura de la tabla
$sentencia = "SHOW FIELDS FROM tabla1";
$resultado = mysqli_query($conexion, $sentencia);

echo "<h3>Estructura de tabla1:</h3>";
echo "<table border='1'>";

while ($campo = mysqli_fetch_row($resultado)) {
    echo "<tr>";
    foreach ($campo as $valor) {
        echo "<td>" . $valor . "</td>";
    }
    echo "</tr>";
}

echo "</table>";

mysqli_close($conexion);
?>
```

---

## 5. Buenas Prácticas y Recomendaciones

### 5.1 Seguridad

#### Prevención de Inyección SQL

**NUNCA** construyas consultas concatenando directamente valores del usuario:

```php
// PELIGROSO - Vulnerable a inyección SQL
$nombre = $_POST['nombre'];
$sql = "SELECT * FROM usuarios WHERE nombre = '$nombre'";
```

** Solución 1: Escapar caracteres especiales**
```php
$nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
$sql = "SELECT * FROM usuarios WHERE nombre = '$nombre'";
```

** Solución 2: Usar Prepared Statements (más seguro)**
```php
$stmt = mysqli_prepare($conexion, "SELECT * FROM usuarios WHERE nombre = ?");
mysqli_stmt_bind_param($stmt, "s", $_POST['nombre']);
mysqli_stmt_execute($stmt);
```

---

### 5.2 Manejo de Errores

```php
// Siempre verificar el resultado de las operaciones
if (!$conexion) {
    error_log("Error de conexión: " . mysqli_connect_error());
    die("Error al conectar con la base de datos");
}

if (!$resultado) {
    error_log("Error en consulta: " . mysqli_error($conexion));
    die("Error al ejecutar la consulta");
}
```

---

### 5.3 Gestión de Recursos

```php
// Liberar memoria de resultados grandes
mysqli_free_result($resultado);

// Siempre cerrar conexiones
mysqli_close($conexion);
```

---

### 5.4 Configuración de Codificación

```php
// Establecer charset UTF-8 para evitar problemas con caracteres especiales
mysqli_set_charset($conexion, "utf8");
// O mejor aún: utf8mb4 para soporte completo de emojis
mysqli_set_charset($conexion, "utf8mb4");
```

---

## 6. Comparación de Métodos de Fetch

| Función | Tipo de Array | Acceso a Datos | Uso |
|---------|---------------|----------------|-----|
| `mysqli_fetch_assoc()` | Asociativo | `$fila['columna']` | Más legible, recomendado |
| `mysqli_fetch_row()` | Numérico | `$fila[0]` | Más rápido, menos legible |
| `mysqli_fetch_array()` | Configurable | Depende del parámetro | Flexible pero más pesado |

---

## 7. Patrones de Uso Comunes

### 7.1 Patrón de Conexión Reutilizable

```php
<?php
// config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mibasedatos');

function conectarDB() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conn, "utf8mb4");
    return $conn;
}
?>
```

---

### 7.2 Patrón de Consulta Segura

```php
<?php
function consultaSegura($conexion, $sql, $params = []) {
    $stmt = mysqli_prepare($conexion, $sql);
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// Uso
$resultado = consultaSegura(
    $conexion, 
    "SELECT * FROM usuarios WHERE email = ? AND activo = ?",
    ["s", $email, "i", 1]
);
?>
```

---

## 8. Resumen de Funciones Clave

| Función | Propósito |
|---------|-----------|
| `mysqli_connect()` | Conectar al servidor MySQL |
| `mysqli_select_db()` | Seleccionar base de datos |
| `mysqli_query()` | Ejecutar consulta SQL |
| `mysqli_fetch_assoc()` | Obtener fila como array asociativo |
| `mysqli_fetch_row()` | Obtener fila como array numérico |
| `mysqli_fetch_array()` | Obtener fila (configurable) |
| `mysqli_num_rows()` | Contar filas del resultado |
| `mysqli_error()` | Obtener mensaje de error |
| `mysqli_errno()` | Obtener código de error |
| `mysqli_close()` | Cerrar conexión |
| `mysqli_real_escape_string()` | Escapar caracteres especiales |
| `mysqli_set_charset()` | Establecer codificación |

---

## 9. Recursos Adicionales

- **Documentación oficial de PHP**: [php.net/manual/es/book.mysqli.php](https://www.php.net/manual/es/book.mysqli.php)
- **Tutorial de MySQLi**: [w3schools.com/php/php_mysql_intro.asp](https://www.w3schools.com/php/php_mysql_intro.asp)
- **Seguridad en PHP**: [owasp.org/www-project-php-security-cheat-sheet](https://owasp.org/)

---

**Fecha del documento**: Noviembre 2025  
**Versión PHP recomendada**: 8.0 o superior  
**Extensión requerida**: mysqli (habilitada por defecto)
