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

---

## Cómo evaluar los proyectos de los alumnos

Hay dos formas de organizar la entrega. Elige la que mejor encaje con tu dinámica de clase.

---

### Opción A — Ramas por alumno (entrega mediante Pull Request)

Es la opción más sencilla sin herramientas externas.

**Flujo:**

1. El alumno clona el repo o hace un fork.
2. Trabaja en una rama con su nombre: `alumno/juan-garcia`.
3. Cuando termina, abre un **Pull Request** a `main`.
4. GitHub Actions ejecuta los workflows automáticamente sobre su código.
5. Tú ves en el PR:
   - ✅ / ❌ por cada comprobación del workflow.
   - El diff completo de su código.
   - Puedes añadir comentarios de revisión línea a línea.

```
alumno hace fork → trabaja en rama → abre PR → GitHub Actions corre → tú revisas y calificas
```

**Ventaja:** todo ocurre en este mismo repo, sin configuración extra.  
**Inconveniente:** los alumnos se ven el código entre ellos si el repo es público (usar repo privado resuelve esto).

---

### Opción B — GitHub Classroom (recomendado para grupos grandes)

[GitHub Classroom](https://classroom.github.com) es gratuito para profesores y automatiza toda la gestión de repos individuales por alumno.

**Flujo:**

1. Entras en [classroom.github.com](https://classroom.github.com) con tu cuenta `AmadorProf`.
2. Creas una **assignment** enlazada a este repo como plantilla.
3. GitHub Classroom genera un **enlace de invitación** que repartes a la clase.
4. Cada alumno acepta el enlace → GitHub les crea automáticamente un **repo privado** con el código base.
5. Los workflows de evaluación corren en el repo de cada alumno cada vez que hacen push.
6. Desde el panel de Classroom ves el estado de todos los alumnos en una sola pantalla.

**Ventaja:** repos privados individuales, dashboard centralizado, sin que los alumnos se vean entre sí.  
**Inconveniente:** requiere configurar Classroom una vez al principio del curso.

---

### Comparativa

| | Opción A (ramas + PR) | Opción B (GitHub Classroom) |
|---|---|---|
| Configuración | Ninguna | 15-20 min inicial |
| Privacidad entre alumnos | Depende del repo | Garantizada (repos privados) |
| Dashboard centralizado | No | Sí |
| Comentarios línea a línea | Sí | Sí |
| Ideal para | Entregas puntuales | Grupos completos de clase |
