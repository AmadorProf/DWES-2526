# Tutorial: Crear tu Primer Proyecto Laravel con PHPStorm y VSCode

Te voy a guiar paso a paso para crear tu primer proyecto Laravel usando ambos editores. Comenzaremos con los requisitos previos y luego veremos cada editor por separado.

## Requisitos Previos (Para Ambos Editores)

Antes de empezar, necesitas tener instalado en tu sistema:

1. **PHP 8.1 o superior**: Verifica con `php -v` en tu terminal
2. **Composer**: El gestor de dependencias de PHP (https://getcomposer.org/)
3. **Node.js y NPM**: Para compilar assets (https://nodejs.org/)
4. **Base de datos**: MySQL, PostgreSQL o SQLite

Para verificar que tienes todo instalado correctamente, ejecuta en tu terminal:

```bash
php -v
composer --version
node -v
npm -v
```

---

## PARTE 1: Crear Proyecto Laravel con PHPStorm

### Paso 1: Instalar PHPStorm

Descarga PHPStorm desde https://www.jetbrains.com/phpstorm/ (tiene versión de prueba de 30 días).

### Paso 2: Crear el Proyecto Laravel

#### Opción A: Crear desde PHPStorm

1. Abre PHPStorm
2. Ve a **File → New → Project**
3. Selecciona **PHP** en el menú izquierdo
4. Selecciona **Laravel** como tipo de proyecto
5. Configura:
   - **Location**: Elige la carpeta donde quieres el proyecto
   - **PHP Interpreter**: Selecciona tu instalación de PHP
6. Click en **Create**

#### Opción B: Crear desde Terminal (recomendado)

1. Abre PHPStorm
2. Ve a **File → New → Project from Existing Files**
3. Pero primero, abre la terminal integrada en PHPStorm (Alt+F12 o View → Tool Windows → Terminal)
4. Navega a la carpeta donde quieres crear el proyecto:

```bash
cd ~/Documents/proyectos
```

5. Ejecuta el comando de Composer para crear el proyecto:

```bash
composer create-project laravel/laravel mi-primer-proyecto
```

6. Espera a que Composer descargue todas las dependencias (puede tardar unos minutos)

### Paso 3: Configurar PHPStorm

1. PHPStorm debería detectar automáticamente que es un proyecto Laravel
2. Si aparece una notificación arriba pidiendo habilitar soporte para Laravel, haz click en **Enable**
3. Ve a **File → Settings** (o PHPStorm → Preferences en Mac)
4. Busca **PHP** en el menú izquierdo y configura:
   - **PHP language level**: Selecciona tu versión de PHP
   - **CLI Interpreter**: Asegúrate de que apunta a tu PHP

### Paso 4: Configurar el Archivo .env

1. En la raíz del proyecto, busca el archivo `.env`
2. Ábrelo y configura tu base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

Si prefieres usar SQLite para simplificar (recomendado para desarrollo):

```env
DB_CONNECTION=sqlite
# Comenta o elimina las siguientes líneas de MySQL
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=...
```

Y crea el archivo de base de datos:

```bash
touch database/database.sqlite
```

### Paso 5: Generar la Application Key

En la terminal de PHPStorm:

```bash
php artisan key:generate
```

### Paso 6: Ejecutar Migraciones

```bash
php artisan migrate
```

### Paso 7: Instalar Dependencias de Frontend

```bash
npm install
```

### Paso 8: Iniciar el Servidor de Desarrollo

En PHPStorm, tienes varias opciones:

#### Opción A: Desde Terminal

```bash
php artisan serve
```

#### Opción B: Configurar Run Configuration

1. Ve a **Run → Edit Configurations**
2. Click en el **+** y selecciona **PHP Built-in Web Server**
3. Configura:
   - **Name**: Laravel Server
   - **Host**: localhost
   - **Port**: 8000
   - **Document root**: Selecciona la carpeta `public` de tu proyecto
4. Click **OK** y luego en el botón de **Play** (▶)

### Paso 9: Verificar la Instalación

Abre tu navegador y ve a: http://localhost:8000

Deberías ver la página de bienvenida de Laravel.

### Consejos para PHPStorm

- **Laravel Idea Plugin**: Instala este plugin para mejorar el soporte de Laravel (Settings → Plugins → busca "Laravel Idea")
- **Database Tools**: PHPStorm incluye herramientas de base de datos integradas (View → Tool Windows → Database)
- **Artisan Commands**: Presiona Ctrl+Shift+A (Cmd+Shift+A en Mac) y escribe "artisan" para ejecutar comandos Laravel
- **Code Completion**: PHPStorm ofrece excelente autocompletado para Laravel

---

## PARTE 2: Crear Proyecto Laravel con VSCode

### Paso 1: Instalar VSCode

Descarga VSCode desde https://code.visualstudio.com/

### Paso 2: Instalar Extensiones Esenciales

Abre VSCode y ve a la pestaña de extensiones (Ctrl+Shift+X o Cmd+Shift+X en Mac) e instala:

1. **PHP Intelephense** (bmewburn.vscode-intelephense-client)
2. **Laravel Extension Pack** (onecentlin.laravel-extension-pack) - que incluye:
   - Laravel Blade Snippets
   - Laravel Snippets
   - Laravel Artisan
   - Laravel Extra Intellisense
3. **PHP Debug** (xdebug.php-debug)
4. **DotENV** (mikestead.dotenv)
5. **Prettier** (esbenp.prettier-vscode) - para formatear código
6. **ESLint** (dbaeumer.vscode-eslint) - para JavaScript

### Paso 3: Crear el Proyecto Laravel

1. Abre VSCode
2. Abre la terminal integrada (Ctrl+` o View → Terminal)
3. Navega a la carpeta donde quieres crear el proyecto:

```bash
cd ~/Documents/proyectos
```

4. Crea el proyecto con Composer:

```bash
composer create-project laravel/laravel mi-primer-proyecto
```

5. Abre el proyecto en VSCode:

```bash
cd mi-primer-proyecto
code .
```

O simplemente arrastra la carpeta del proyecto a VSCode.

### Paso 4: Configurar el Archivo .env

1. Abre el archivo `.env` en la raíz del proyecto
2. Configura tu base de datos igual que en el ejemplo de PHPStorm

Para SQLite:

```env
DB_CONNECTION=sqlite
```

Y crea el archivo:

```bash
touch database/database.sqlite
```

### Paso 5: Generar la Application Key

En la terminal de VSCode:

```bash
php artisan key:generate
```

### Paso 6: Ejecutar Migraciones

```bash
php artisan migrate
```

### Paso 7: Instalar Dependencias de Frontend

```bash
npm install
```

### Paso 8: Configurar Atajos para Artisan (Opcional)

1. Presiona Ctrl+Shift+P (Cmd+Shift+P en Mac) para abrir la paleta de comandos
2. Escribe "Laravel Artisan" y verás comandos disponibles
3. O simplemente usa la terminal para ejecutar comandos artisan

### Paso 9: Iniciar el Servidor de Desarrollo

En la terminal de VSCode:

```bash
php artisan serve
```

Si necesitas compilar assets en tiempo real, abre una segunda terminal (click en el **+** en la terminal) y ejecuta:

```bash
npm run dev
```

### Paso 10: Verificar la Instalación

Abre tu navegador y ve a: http://localhost:8000

### Consejos para VSCode

#### Configuración Recomendada para PHP

Crea un archivo `.vscode/settings.json` en tu proyecto:

```json
{
    "php.suggest.basic": false,
    "intelephense.stubs": [
        "apache",
        "bcmath",
        "Core",
        "date",
        "dom",
        "json",
        "mbstring",
        "mysqli",
        "openssl",
        "pcre",
        "PDO",
        "pdo_mysql",
        "Phar",
        "SimpleXML",
        "sockets",
        "SPL",
        "tokenizer",
        "xml",
        "xmlwriter",
        "zip",
        "zlib"
    ],
    "files.associations": {
        "*.blade.php": "blade"
    },
    "[blade]": {
        "editor.defaultFormatter": "shufo.vscode-blade-formatter"
    }
}
```

#### Atajos Útiles en VSCode

- **Ctrl+P** (Cmd+P): Búsqueda rápida de archivos
- **Ctrl+Shift+F**: Buscar en todo el proyecto
- **Ctrl+`**: Abrir/cerrar terminal
- **Alt+Click**: Múltiples cursores

---

## Tu Primera Ruta y Vista

Para probar que todo funciona, vamos a crear una ruta y vista simple (funciona igual en ambos editores):

### 1. Crear una Ruta

Abre `routes/web.php` y añade:

```php
Route::get('/hola', function () {
    return view('hola', ['nombre' => 'Mundo']);
});
```

### 2. Crear una Vista

Crea un archivo `resources/views/hola.blade.php` con:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Mi Primera Vista</title>
</head>
<body>
    <h1>¡Hola {{ $nombre }}!</h1>
    <p>Esta es mi primera aplicación Laravel</p>
</body>
</html>
```

### 3. Probar

Visita en tu navegador: http://localhost:8000/hola

---

## Recursos Adicionales

- **Documentación Oficial de Laravel**: https://laravel.com/docs
- **Laracasts**: https://laracasts.com (tutoriales en video)
- **Laravel News**: https://laravel-news.com
- **Comunidad en Discord**: https://discord.gg/laravel

---

## Solución de Problemas Comunes

### Error: "No application encryption key has been specified"

**Solución:**
```bash
php artisan key:generate
```

### Error al ejecutar migraciones

**Solución:** Verifica que tu archivo `.env` tenga la configuración correcta de base de datos y que la base de datos exista.

### Puerto 8000 ya en uso

**Solución:** Especifica un puerto diferente:
```bash
php artisan serve --port=8001
```

### Problemas con permisos en Linux/Mac

**Solución:**
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

---

¡Felicidades! Has completado tu primer proyecto Laravel. 
