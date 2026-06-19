# Tutorial: Crear tu Primer Proyecto Laravel con PHPStorm y VSCode

## Requisitos Previos (Para Ambos Editores)

Antes de empezar, necesitas tener instalado en tu sistema:

1. **PHP 8.1 o superior**: Verifica con `php -v` en tu terminal
2. **Composer**: El gestor de dependencias de PHP (https://getcomposer.org/)
3. **Node.js y NPM**: Para compilar assets (https://nodejs.org/)
4. **Base de datos**: MySQL, PostgreSQL o SQLite

### Instalación en Windows con XAMPP

Si estás en Windows y prefieres usar XAMPP, sigue estos pasos:

#### Paso 1: Instalar XAMPP

1. Descarga XAMPP desde https://www.apachefriends.org/
2. Ejecuta el instalador y sigue las instrucciones
3. Instala en la ruta por defecto: `C:\xampp`
4. Durante la instalación, asegúrate de seleccionar Apache, MySQL y PHP

#### Paso 2: Configurar PHP en el PATH de Windows

Para poder usar PHP desde cualquier terminal:

1. Abre el Panel de Control
2. Ve a Sistema y Seguridad > Sistema > Configuración avanzada del sistema
3. Click en "Variables de entorno"
4. En "Variables del sistema", busca la variable "Path" y haz doble click
5. Click en "Nuevo" y agrega: `C:\xampp\php`
6. Click en "Aceptar" en todas las ventanas
7. Cierra y vuelve a abrir cualquier terminal para que tome efecto

#### Paso 3: Verificar la Instalación de PHP

Abre una nueva terminal (CMD o PowerShell) y ejecuta:

```bash
php -v
```

Deberías ver la versión de PHP instalada con XAMPP.

#### Paso 4: Instalar Composer en Windows

1. Descarga Composer desde https://getcomposer.org/download/
2. Ejecuta el instalador `Composer-Setup.exe`
3. El instalador debería detectar automáticamente tu PHP de XAMPP
4. Si no lo detecta, indica manualmente: `C:\xampp\php\php.exe`
5. Completa la instalación

#### Paso 5: Verificar Composer

En la terminal:

```bash
composer --version
```

#### Paso 6: Instalar Node.js en Windows

1. Descarga Node.js desde https://nodejs.org/
2. Ejecuta el instalador y sigue las instrucciones
3. Marca la opción de "Automatically install the necessary tools"
4. Completa la instalación

#### Paso 7: Verificar Node.js y NPM

```bash
node -v
npm -v
```

#### Paso 8: Iniciar MySQL con XAMPP

1. Abre el Panel de Control de XAMPP
2. Click en "Start" junto a Apache (opcional, si quieres usarlo)
3. Click en "Start" junto a MySQL
4. Verifica que el estado sea "Running" en verde

### Verificación Final en Cualquier Sistema

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
   - En Windows con XAMPP: `C:\xampp\htdocs\mi-primer-proyecto`
   - **PHP Interpreter**: Selecciona tu instalación de PHP
   - En Windows con XAMPP: `C:\xampp\php\php.exe`
6. Click en **Create**

#### Opción B: Crear desde Terminal (recomendado)

1. Abre PHPStorm
2. Abre la terminal integrada en PHPStorm (Alt+F12 o View → Tool Windows → Terminal)
3. Navega a la carpeta donde quieres crear el proyecto:

En Linux/Mac:
```bash
cd ~/Documents/proyectos
```

En Windows con XAMPP:
```bash
cd C:\xampp\htdocs
```

4. Ejecuta el comando de Composer para crear el proyecto:

```bash
composer create-project laravel/laravel mi-primer-proyecto
```

5. Espera a que Composer descargue todas las dependencias (puede tardar unos minutos)

6. Abre el proyecto en PHPStorm:

```bash
cd mi-primer-proyecto
```

Luego en PHPStorm: File → Open → Selecciona la carpeta del proyecto

### Paso 3: Configurar PHPStorm

1. PHPStorm debería detectar automáticamente que es un proyecto Laravel
2. Si aparece una notificación arriba pidiendo habilitar soporte para Laravel, haz click en **Enable**
3. Ve a **File → Settings** (o PHPStorm → Preferences en Mac)
4. Busca **PHP** en el menú izquierdo y configura:
   - **PHP language level**: Selecciona tu versión de PHP
   - **CLI Interpreter**: Asegúrate de que apunta a tu PHP
   - En Windows con XAMPP: `C:\xampp\php\php.exe`

### Paso 4: Configurar el Archivo .env

1. En la raíz del proyecto, busca el archivo `.env`
2. Ábrelo y configura tu base de datos

#### Para MySQL con XAMPP en Windows:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=root
DB_PASSWORD=
```

**Importante**: En XAMPP por defecto, el usuario es `root` y la contraseña está vacía.

#### Crear la Base de Datos en XAMPP:

1. Abre tu navegador y ve a: http://localhost/phpmyadmin
2. Click en "Nueva" en el menú izquierdo
3. Nombre de la base de datos: `laravel_db`
4. Cotejamiento: `utf8mb4_unicode_ci`
5. Click en "Crear"

#### Para SQLite (alternativa más simple):

```env
DB_CONNECTION=sqlite
# Comenta las líneas de MySQL:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel_db
# DB_USERNAME=root
# DB_PASSWORD=
```

Y crea el archivo de base de datos:

En Linux/Mac:
```bash
touch database/database.sqlite
```

En Windows (PowerShell):
```powershell
New-Item database/database.sqlite -ItemType File
```

O en Windows (CMD):
```cmd
type nul > database\database.sqlite
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
4. Click **OK** y luego en el botón de **Play**

#### Nota para Windows con XAMPP:

Si prefieres usar Apache de XAMPP en lugar del servidor integrado de PHP:

1. Asegúrate de que Apache esté corriendo en el Panel de Control de XAMPP
2. Tu proyecto debería estar en `C:\xampp\htdocs\mi-primer-proyecto`
3. Accede mediante: http://localhost/mi-primer-proyecto/public

Sin embargo, es recomendable usar `php artisan serve` para desarrollo, ya que Laravel está optimizado para esto.

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

En Linux/Mac:
```bash
cd ~/Documents/proyectos
```

En Windows con XAMPP:
```bash
cd C:\xampp\htdocs
```

4. Crea el proyecto con Composer:

```bash
composer create-project laravel/laravel mi-primer-proyecto
```

5. Abre el proyecto en VSCode:

En Linux/Mac:
```bash
cd mi-primer-proyecto
code .
```

En Windows:
```cmd
cd mi-primer-proyecto
code .
```

O simplemente arrastra la carpeta del proyecto a VSCode.

### Paso 4: Configurar el Archivo .env

1. Abre el archivo `.env` en la raíz del proyecto
2. Configura tu base de datos

#### Para MySQL con XAMPP:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=root
DB_PASSWORD=
```

Recuerda crear la base de datos en phpMyAdmin como se explicó anteriormente.

#### Para SQLite:

```env
DB_CONNECTION=sqlite
```

Y crea el archivo según tu sistema operativo (ver comandos en la sección de PHPStorm).

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
    "php.validate.executablePath": "C:\\xampp\\php\\php.exe",
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

**Nota para Windows**: Ajusta la ruta `php.validate.executablePath` según tu instalación:
- Con XAMPP: `"C:\\xampp\\php\\php.exe"`
- En Linux/Mac, puedes omitir esta línea o usar: `"/usr/bin/php"`

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
    <h1>Hola {{ $nombre }}</h1>
    <p>Esta es mi primera aplicación Laravel</p>
</body>
</html>
```

### 3. Probar

Visita en tu navegador: http://localhost:8000/hola

---

## Comparación: PHPStorm vs VSCode para Laravel

### PHPStorm

**Ventajas:**
- Mejor autocompletado y refactoring automático
- Herramientas de base de datos integradas
- Debugging más potente
- Mejor para proyectos grandes
- Integración nativa con Composer y Artisan
- Análisis de código más profundo

**Desventajas:**
- Es de pago (después de 30 días de prueba)
- Consume más recursos (RAM y CPU)
- Puede ser lento en equipos con pocos recursos
- Curva de aprendizaje más pronunciada

### VSCode

**Ventajas:**
- Gratuito y open source
- Más ligero y rápido
- Gran ecosistema de extensiones
- Mejor integración con Git
- Más personalizable
- Comunidad muy activa

**Desventajas:**
- Requiere configurar extensiones manualmente
- Autocompletado menos preciso que PHPStorm
- Necesita más configuración inicial
- Debugging menos intuitivo

---

## Configuración de Virtual Hosts en XAMPP (Opcional)

Si quieres acceder a tu proyecto con una URL personalizada como `http://mi-proyecto.test` en lugar de `http://localhost:8000`:

### En Windows:

1. Edita el archivo de hosts:
   - Abre Bloc de notas como Administrador
   - Abre el archivo: `C:\Windows\System32\drivers\etc\hosts`
   - Añade al final: `127.0.0.1 mi-proyecto.test`
   - Guarda el archivo

2. Edita el archivo de Virtual Hosts de Apache:
   - Abre: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
   - Añade al final:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/mi-primer-proyecto/public"
    ServerName mi-proyecto.test
    <Directory "C:/xampp/htdocs/mi-primer-proyecto/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Reinicia Apache desde el Panel de Control de XAMPP
4. Accede a: http://mi-proyecto.test

### En Linux/Mac:

1. Edita el archivo de hosts:
```bash
sudo nano /etc/hosts
```
Añade: `127.0.0.1 mi-proyecto.test`

2. Si usas Apache, edita el archivo de Virtual Hosts según tu distribución

---

## Próximos Pasos

Ahora que tienes tu proyecto Laravel funcionando, puedes:

1. Aprender sobre Eloquent ORM para trabajar con bases de datos
2. Crear controladores con `php artisan make:controller`
3. Explorar Blade templates
4. Aprender sobre middleware y autenticación
5. Revisar la documentación oficial: https://laravel.com/docs

---

## Recursos Adicionales

- **Documentación Oficial de Laravel**: https://laravel.com/docs
- **Laracasts**: https://laracasts.com (tutoriales en video)
- **Laravel News**: https://laravel-news.com
- **Comunidad en Discord**: https://discord.gg/laravel
- **Foro oficial**: https://laracasts.com/discuss

---

## Solución de Problemas Comunes

### Error: "No application encryption key has been specified"

**Solución:**
```bash
php artisan key:generate
```

### Error al ejecutar migraciones

**Solución:** Verifica que tu archivo `.env` tenga la configuración correcta de base de datos y que la base de datos exista.

Para MySQL con XAMPP, verifica en phpMyAdmin que la base de datos existe.

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

### Error: "Class 'PDO' not found" en Windows con XAMPP

**Solución:**
1. Abre el archivo `C:\xampp\php\php.ini`
2. Busca y descomenta (quita el punto y coma) estas líneas:
```ini
extension=pdo_mysql
extension=pdo_sqlite
extension=openssl
extension=mbstring
```
3. Guarda y reinicia XAMPP

### Composer muy lento en Windows

**Solución:**
```bash
composer config -g repo.packagist composer https://packagist.org
composer global require hirak/prestissimo
```

### npm install falla en Windows

**Solución:**
Ejecuta PowerShell como Administrador y ejecuta:
```powershell
Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned
```

---

## Comandos Artisan Más Útiles

```bash
# Ver lista de todos los comandos
php artisan list

# Crear un controlador
php artisan make:controller NombreController

# Crear un modelo
php artisan make:model NombreModelo

# Crear una migración
php artisan make:migration crear_tabla_usuarios

# Crear un seeder
php artisan make:seeder UsuariosSeeder

# Ejecutar migraciones
php artisan migrate

# Revertir última migración
php artisan migrate:rollback

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ver rutas definidas
php artisan route:list
```

---
