# MountainConnect - Plataforma Web Montañera

## Descripción del Proyecto

MountainConnect es una aplicación web desarrollada con PHP y MySQL como proyecto educativo. Permite a los usuarios compartir y descubrir rutas de senderismo, vías ferratas y escalada, creando una comunidad de montañeros.

## Objetivos de Aprendizaje

- Desarrollo de aplicaciones web dinámicas con PHP
- Diseño e implementación de bases de datos con MySQL
- Gestión de sesiones y autenticación de usuarios
- Operaciones CRUD completas
- Validación de formularios y subida de archivos
- Aplicación de medidas de seguridad básicas

## Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de datos**: MySQL 8.0 / MariaDB (Fase 2)
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **Servidor local**: XAMPP, WAMP o similar
- **Control de versiones**: Git

## Estructura del Proyecto

```
mountain-connect/
│
├── Dockerfile-php                 # Dockerfile para el servicio php (opcional)
├── docker-compose.yml
├── readme.md
├── docker/
│   ├── nginx/
│   │   └── conf.d/
│   │       └── default.conf       # Config nginx (sitio)
│   └── php/
│       ├── Dockerfile-php         # (alternativa local si la necesitas)
│       ├── entrypoint.sh
│       ├── php.ini
│       └── uploads/               # estructura vacía para permisos/volumen
│           ├── photos/
│           └── profiles/
│
├── config/
│   └── config.php           # Configuración general
│
├── includes/
│   ├── header.php           # Cabecera común
│   ├── footer.php           # Pie de página
│   ├── functions.php        # Funciones auxiliares
│   └── auth_check.php       # Verificación de autenticación
│
├── public/
│   ├── index.php            # Página principal
│   ├── login.php            # Inicio de sesión
│   ├── register.php         # Registro de usuarios
│   ├── logout.php           # Cerrar sesión
│   ├── profile.php          # Perfil de usuario
│   │
│   ├── routes/
│   │   ├── create.php       # Crear ruta
│   │   ├── list.php         # Listado de rutas
│   │   ├── view.php         # Ver detalle de ruta
│   │   ├── edit.php         # Editar ruta
│   │   └── delete.php       # Eliminar ruta
│   │
│   ├── ferratas/            # (Próxima fase)
│   ├── climbing/            # (Próxima fase)
│   └── photos/              # (Próxima fase)
│
├── uploads/
│   ├── photos/              # Fotos de rutas
│   └── profiles/            # Fotos de perfil
│
└── assets/
    ├── css/                 # Estilos personalizados
    ├── js/                  # JavaScript
    └── images/              # Imágenes del sitio
```

## Instalación y Configuración

### Requisitos Previos

- **Docker Desktop** instalado (Windows / macOS) o **Docker Engine + Docker Compose** (Linux).  
- **Puertos libres:** 8080 (para Nginx) y 3306 (para MySQL, si se utiliza).  
- **Permisos de escritura** en la carpeta `uploads/` del proyecto.

### Estructura del entorno Docker

El proyecto se estructura en **tres servicios principales**:
- **PHP-FPM:** Contenedor donde se ejecuta la aplicación PHP.  
- **Nginx:** Servidor web que gestiona las peticiones HTTP y las dirige al backend PHP.  
- **Base de datos (MySQL o MariaDB):** Servicio opcional, previsto para futuras fases.  

Los directorios `public/`, `includes/`, `config/`, `assets/` y `uploads/` se montan como volúmenes para permitir el desarrollo en tiempo real sin necesidad de reconstruir las imágenes.

### Pasos de Instalación

1. **Iniciar el entorno**
   - Desde la raíz del proyecto, ejecuta:
     ```bash
     docker compose up -d
     ```
   - Esto construirá las imágenes (si es la primera vez) y levantará los contenedores necesarios.

2. **Verificar que todo está en ejecución**
   ```bash
   docker ps
   ```
   Deberías ver los contenedores `mountain_php`, `mountain_nginx` y, opcionalmente, `mountain_db`.

3. **Acceder a la aplicación**
   - Abre tu navegador y visita:  [http://localhost:8080](http://localhost:8080)

4. **Subidas y persistencia**
   - Los archivos subidos (fotos de perfil, rutas, etc.) se guardan en `uploads/`.
   - Este directorio está montado como volumen, por lo que los datos se conservan aunque se reinicien los contenedores.

5. **Detener el entorno**
   ```bash
   docker compose down
   ```

6. **Reiniciar o reconstruir (si cambias algo en Docker)**
   ```bash
   docker compose up -d --build
   ```


## Funcionalidades Implementadas - Fase 1

### Sistema de Usuarios

- **Registro de usuarios** con validación completa
  - Validación de formato de email
  - Validación de longitud de contraseña
  - Confirmación de contraseña
  - Niveles de experiencia
  - Especialidades montañeras
  - Selección de provincia

- **Inicio de sesión**
  - Login con usuario o email
  - Gestión de sesiones
  - Hash de contraseñas con `password_hash()`

- **Perfil de usuario**
  - Visualización de datos personales
  - Estadísticas de actividad
  - Rutas creadas por el usuario

### Gestión de Rutas (CRUD Completo)

- **Crear rutas** con información completa:
  - Nombre, dificultad, distancia
  - Desnivel positivo y duración
  - Niveles técnico y físico
  - Descripción detallada
  - Época recomendada
  - Subida de múltiples fotografías

- **Listar rutas**
  - Vista de tarjetas con información resumida
  - Miniaturas de fotografías
  - Filtros por dificultad y provincia
  - Indicadores visuales de características

- **Ver detalle de ruta**
  - Información completa
  - Galería de fotos con carrusel
  - Datos técnicos destacados
  - Información del creador

- **Editar rutas**
  - Solo disponible para el creador
  - Actualización de todos los campos
  - Mantiene las fotografías originales

- **Eliminar rutas**
  - Solo disponible para el creador
  - Eliminación de fotos asociadas
  - Confirmación de eliminación

### Subida y Gestión de Archivos

- Validación de tipo de archivo (JPG, JPEG, PNG)
- Validación de tamaño máximo (2MB)
- Renombrado único de archivos
- Almacenamiento seguro en carpeta `uploads/`
- Eliminación de archivos al borrar rutas

### Validaciones y Seguridad

- Sanitización de entrada con `htmlspecialchars()`
- Validación de formularios del lado del servidor
- Hash de contraseñas con `password_hash()`
- Verificación de sesiones activas
- Protección de páginas privadas
- Verificación de propiedad de contenido

## Características de Diseño

- Interfaz responsive con Bootstrap 5
- Diseño temático montañero
- Iconos de Font Awesome
- Mensajes de feedback claros
- Navegación intuitiva
- Formularios con validación visual

## 📊 Almacenamiento de Datos - Fase 1

En la Fase 1, los datos se almacenan temporalmente en **sesiones de PHP** (`$_SESSION`):

- `$_SESSION['users']`: Array de usuarios registrados
- `$_SESSION['routes']`: Array de rutas creadas
- `$_SESSION['user_id']`: ID del usuario logueado
- `$_SESSION['user_data']`: Datos del usuario actual

> **Nota**: Los datos se pierden al cerrar el navegador o reiniciar el servidor. En la Fase 2 se implementará persistencia con MySQL.

## 🔜 Próximas Fases

### Fase 2: Integración con Base de Datos
- Diseño de esquema de base de datos
- Migración de datos de sesión a MySQL
- Queries preparadas para prevenir SQL injection
- Relaciones entre tablas

### Fase 3: Refactorización POO
- Clases para Usuario, Ruta, etc.
- Patrón MVC (Modelo-Vista-Controlador)
- Separación de lógica de negocio
- Código más mantenible y escalable

### Funcionalidades Futuras
- Sistema de comentarios
- Sistema de valoraciones/likes
- Galería de fotografías independiente
- Vías ferratas (CRUD completo)
- Vías de escalada (CRUD completo)
- Panel de administración
- Sistema de búsqueda avanzada
- Mapa interactivo de rutas
- Exportación de rutas a GPX

## Pruebas

Para probar la aplicación:

1. Registra un nuevo usuario
2. Inicia sesión con las credenciales creadas
3. Crea una nueva ruta con fotografías
4. Explora el listado de rutas
5. Visualiza el detalle de una ruta
6. Edita una ruta que hayas creado
7. Elimina una ruta

## Guía de Uso para Estudiantes

### Concepto Básicos Implementados

1. **Variables y Superglobales**
   - `$_POST`, `$_GET`, `$_SESSION`, `$_FILES`
   - Variables locales y globales

2. **Estructuras de Control**
   - Condicionales: `if`, `else`, `elseif`
   - Bucles: `foreach`, `for`
   - Switch-case para menús

3. **Funciones**
   - Declaración y llamada
   - Parámetros y valores de retorno
   - Funciones auxiliares reutilizables

4. **Formularios**
   - Métodos GET y POST
   - Validación del lado del servidor
   - Mantenimiento de valores

5. **Sesiones**
   - `session_start()`
   - Almacenamiento de datos
   - Verificación de autenticación

6. **Archivos**
   - Subida con `$_FILES`
   - `move_uploaded_file()`
   - Validación de tipo y tamaño

## Solución de Problemas

### Error: "Headers already sent"
- Asegúrate de que no haya salida (echo, espacios) antes de `header()`
- Verifica que los archivos no tengan BOM

### Las imágenes no se suben
- Verifica permisos de escritura en `uploads/`
- Comprueba `upload_max_filesize` en `php.ini`
- Verifica que el formulario tenga `enctype="multipart/form-data"`

### Los datos se pierden al cerrar el navegador
- Esto es normal en Fase 1 (datos en sesión)
- Se solucionará en Fase 2 con MySQL

## Contribución

Este es un proyecto educativo. Se aceptan mejoras en:
- Validaciones adicionales
- Mejoras de UI/UX
- Funcionalidades nuevas
- Corrección de bugs
- Documentación
