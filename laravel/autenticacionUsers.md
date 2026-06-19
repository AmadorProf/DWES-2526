# Autenticación y Gestión de Usuarios en Laravel

## 1. Conceptos Clave: Autenticación vs Autorización

En Laravel es fundamental distinguir entre **autenticación** y **autorización**, ya que resuelven problemas distintos dentro de la seguridad de una aplicación.

### 1.1 Autenticación (Auth)

La autenticación responde a la pregunta:

> ¿Quién es el usuario?

Se encarga de verificar la identidad del usuario, normalmente mediante **email y contraseña**.

En este proyecto:

* Se utiliza un *starter kit* o *scaffolding*.
* Herramienta recomendada: **Laravel Breeze** (alternativa válida: Laravel UI).
* Breeze genera automáticamente:

  * Formularios de registro, login y logout
  * Controladores
  * Rutas
  * Lógica de autenticación

#### Seguridad de contraseñas

Laravel **nunca** guarda contraseñas en texto plano. Utiliza hashing con `bcrypt` de forma automática:

```php
use Illuminate\Support\Facades\Hash;

$user->password = Hash::make($request->password);
```

Cuando se usa Breeze, este proceso ya viene implementado correctamente.

---

### 1.2 Autorización (Roles y Permisos)

La autorización responde a la pregunta:

> ¿Qué puede hacer el usuario autenticado?

En el proyecto se implementa mediante:

* Un sistema de **roles**

  * `admin`
  * `user`
* Control de acceso usando **Middleware**

Ejemplo de reglas:

* Un usuario normal puede:

  * Crear su propio contenido
  * Escribir reseñas
* Un administrador puede además:

  * Acceder al panel de administración
  * Eliminar o gestionar contenido de otros usuarios

---

## 2. Estructura de Base de Datos: Tabla `users`

Laravel incluye por defecto una tabla `users`, pero es necesario adaptarla para soportar roles.

### 2.1 Migración

Se añade una columna `role` a la tabla `users`.

```php
Schema::table('users', function (Blueprint $table) {
    $table->enum('role', ['user', 'admin'])->default('user');
});
```

#### Detalles importantes

* **Tipo de dato**: `ENUM`

  * Restringe los valores posibles
  * Evita errores y datos inconsistentes
* **Valor por defecto**: `user`

  * Cualquier persona que se registre será usuario normal
  * Principio de seguridad por defecto

---

### 2.2 Relaciones

El modelo `User` mantiene relaciones con otras entidades del proyecto.

Ejemplos:

```php
// Un usuario tiene muchos contenidos
public function contents()
{
    return $this->hasMany(Content::class);
}

// Un usuario tiene muchas reseñas
public function reviews()
{
    return $this->hasMany(Review::class);
}
```

Estas relaciones permiten:

* Obtener el autor de un contenido
* Limitar acciones solo al creador o a un administrador

---

## 3. Modelo User: Lógica de Negocio

Archivo: `app/Models/User.php`

### 3.1 Asignación masiva ($fillable)

Para permitir que el campo `role` se guarde correctamente:

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',
];
```

Si no se añade, Laravel ignorará este campo al crear o actualizar usuarios.

---

### 3.2 Métodos Helper para roles

Para evitar repetir comparaciones en todo el código, se crean métodos auxiliares:

```php
public function isAdmin()
{
    return $this->role === 'admin';
}

public function isUser()
{
    return $this->role === 'user';
}
```

Uso práctico:

```php
if (auth()->user()->isAdmin()) {
    // lógica de administrador
}
```

Esto mejora:

* Legibilidad
* Mantenimiento del código
* Reutilización

---

## 4. Protección de Rutas con Middleware

Un **Middleware** actúa como un filtro que intercepta las peticiones HTTP antes de llegar al controlador.

### 4.1 Middleware de Autenticación (`auth`)

Incluido por defecto en Laravel.

Función:

* Verifica si el usuario está logueado
* Si no lo está, lo redirige al login

Ejemplo de uso:

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/content/create', [ContentController::class, 'create']);
});
```

---

### 4.2 Middleware de Rol: IsAdmin

Se debe crear manualmente:

```bash
php artisan make:middleware IsAdmin
```

Implementación básica:

```php
public function handle($request, Closure $next)
{
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        abort(403);
    }

    return $next($request);
}
```

Registro en `Kernel.php`:

```php
protected $routeMiddleware = [
    'admin' => \App\Http\Middleware\IsAdmin::class,
];
```

Uso en rutas:

```php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});
```

---

## 5. Generación de Datos: Seeders y Factories

Durante el desarrollo **no se crean usuarios manualmente** en la base de datos.

### 5.1 Seeder de Administrador

Crea un usuario administrador con credenciales conocidas.

```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@admin.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
]);
```

Esto permite:

* Acceder al panel de administración
* Probar permisos sin depender del registro

---

### 5.2 Seeder de Usuarios normales

Se utiliza un **Factory**:

```php
User::factory(3)->create([
    'role' => 'user'
]);
```

Ventajas:

* Datos realistas
* Pruebas de paginación
* Pruebas de relaciones entre modelos

---

## 6. Flujo de Trabajo Resumido

1. Instalar Breeze

   ```bash
   composer require laravel/breeze
   php artisan breeze:install
   ```

2. Modificar la base de datos

   * Añadir columna `role`
   * Ejecutar migraciones

   ```bash
   php artisan migrate
   ```

3. Configurar lógica

   * Métodos `isAdmin()` en el modelo User
   * Middleware `IsAdmin`

4. Proteger rutas

   * Usar `auth` y `admin` según corresponda

5. Generar datos de prueba

   * Seeder de administrador
   * Factory de usuarios normales

