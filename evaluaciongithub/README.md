# Evaluación automática con GitHub Actions

Los workflows en `.github/workflows/` se ejecutan automáticamente cuando se hace push o se abre un Pull Request sobre los proyectos.

## Proyectos evaluados

### PHP — Mountain Connect (`evaluar-php.yml`)

Se dispara al modificar cualquier archivo en `PHP/proyecto/`.

| Comprobación | Descripción |
|---|---|
| **Sintaxis PHP** | Ejecuta `php -l` sobre todos los `.php` del proyecto |
| **Login page → 200** | La página de login responde HTTP 200 |
| **Formulario de login** | El HTML contiene un `<form>` |
| **Registro responde** | `register.php` devuelve 200 o 302 |
| **Listado de rutas** | `routes/list.php` responde (pública o redirigida) |
| **Creación de ruta** | `routes/create.php` responde |
| **Sin errores en logs** | Los logs de PHP no contienen `Fatal error` ni `Parse error` |

Infraestructura usada: **Docker Compose** (PHP-FPM + Nginx + MySQL 8).

---

### Laravel — Examen MovieHub (`evaluar-laravel.yml`)

Se dispara al modificar cualquier archivo en `laravel/Examen/`.

#### Job: PHPUnit / Pest

| Paso | Descripción |
|---|---|
| Instalar dependencias | `composer install` |
| Configurar `.env` | Base de datos MySQL de test |
| Migraciones | `php artisan migrate` |
| Seeders | `php artisan db:seed` |
| Tests | `php artisan test` |

#### Job: Estructura del proyecto

Comprueba que el alumno ha entregado todos los ficheros exigidos:

- Controladores: `MovieController`, `ReviewController`, `RatingController`, `AdminController`
- Modelos: `Movie`, `Review`, `Rating`, `User`
- Al menos 4 migraciones
- Seeders: `MovieSeeder`, `UserSeeder`
- Vistas CRUD de movies (`index`, `show`, `create`, `edit`)
- Middleware `IsAdmin`
- Al menos 5 rutas en `web.php`

---

## Cómo ver los resultados

1. Ir al repo en GitHub → pestaña **Actions**
2. Seleccionar el workflow correspondiente
3. Cada job muestra en verde (OK) o rojo (FALLO) cada comprobación

## Uso con Pull Requests de alumnos

Si los alumnos entregan su trabajo como Pull Request al repo, los workflows se ejecutan automáticamente sobre su código y el resultado aparece directamente en el PR antes de aceptarlo.
