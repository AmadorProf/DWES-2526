# Actividad Práctica: Sistema de Biblioteca en Laravel

## Objetivo
Crear un sistema básico de gestión de libros aplicando los conceptos de Modelos, Vistas y Controladores (MVC).

## Descripción del Proyecto
Los estudiantes desarrollarán una aplicación web para gestionar el catálogo de una biblioteca, donde podrán listar, ver detalles, crear, editar y eliminar libros.

---

## Requisitos Funcionales

### 1. Modelo: Libro
Crear un modelo `Book` con los siguientes campos:
- `id` (auto-incremental)
- `titulo` (string, obligatorio)
- `autor` (string, obligatorio)
- `isbn` (string, único)
- `año_publicacion` (integer)
- `editorial` (string)
- `disponible` (boolean, por defecto true)
- `timestamps` (created_at, updated_at)

### 2. Controlador: BookController
Crear un controlador con los siguientes métodos:

- **index()**: Mostrar listado de todos los libros
- **show($id)**: Mostrar detalles de un libro específico
- **create()**: Mostrar formulario para crear un nuevo libro
- **store(Request $request)**: Guardar un nuevo libro en la base de datos
- **edit($id)**: Mostrar formulario para editar un libro existente
- **update(Request $request, $id)**: Actualizar los datos de un libro
- **destroy($id)**: Eliminar un libro

### 3. Vistas
Crear las siguientes vistas en `resources/views/books/`:

**index.blade.php**
- Tabla con todos los libros (título, autor, año, disponibilidad)
- Botón para agregar nuevo libro
- Botones de acción para cada libro: Ver, Editar, Eliminar

**show.blade.php**
- Mostrar todos los detalles de un libro
- Botón para volver al listado
- Botón para editar

**create.blade.php**
- Formulario con todos los campos del libro
- Botones: Guardar y Cancelar

**edit.blade.php**
- Formulario prellenado con los datos actuales del libro
- Botones: Actualizar y Cancelar

---

## Pasos a Seguir

### Paso 1: Crear la Migración
```bash
php artisan make:migration create_books_table
```

En el archivo de migración, definir la estructura de la tabla.

### Paso 2: Crear el Modelo
```bash
php artisan make:model Book
```

Configurar los atributos `$fillable` para permitir asignación masiva.

### Paso 3: Crear el Controlador
```bash
php artisan make:controller BookController --resource
```

Implementar los 7 métodos del controlador.

### Paso 4: Definir las Rutas
En `routes/web.php`, agregar las rutas para el recurso de libros.

### Paso 5: Crear las Vistas
Desarrollar las 4 vistas necesarias utilizando Blade.

### Paso 6: Ejecutar la Migración
```bash
php artisan migrate
```

---

## Retos Adicionales (Opcional)

Para estudiantes que terminen antes:

1. **Layout maestro**: Crear un archivo `layout.blade.php` y extenderlo en todas las vistas
2. **Validación**: Agregar validación de datos en el método `store()` y `update()`
3. **Mensajes flash**: Mostrar mensajes de éxito/error después de cada operación
4. **Búsqueda**: Agregar un campo de búsqueda por título o autor
5. **Paginación**: Implementar paginación en el listado de libros

---

## Recursos de Ayuda

- Documentación oficial de Laravel: https://laravel.com/docs
- Comando para ver todas las rutas: `php artisan route:list`
- Comando para limpiar caché: `php artisan cache:clear`
