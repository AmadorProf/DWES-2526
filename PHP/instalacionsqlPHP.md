# Manual de Configuración: PHP con MySQL usando XAMPP

## Tabla de Contenidos
1. [Instalación en Windows](#windows)
2. [Instalación en Linux](#linux)
3. [Configuración Inicial](#configuración-inicial)
4. [Primeros Pasos](#primeros-pasos)
5. [Configuración de PHP](#configuración-php)
6. [Configuración de MySQL](#configuración-mysql)
7. [Problemas Comunes](#problemas-comunes)
8. [Herramientas Útiles](#herramientas-útiles)

---

## 🪟 Instalación en Windows {#windows}

### 1. Descargar XAMPP

1. Visita la página oficial: [https://www.apachefriends.org](https://www.apachefriends.org)
2. Descarga la versión para Windows (archivo `.exe`)
3. Elige la versión que incluya:
   - Apache
   - MySQL
   - PHP
   - phpMyAdmin

### 2. Ejecutar el Instalador

1. **Ejecutar como administrador**
   - Clic derecho sobre el archivo descargado
   - Seleccionar "Ejecutar como administrador"

2. **Desactivar UAC (opcional pero recomendado)**
   - Si aparece advertencia de UAC (Control de Cuentas de Usuario)
   - Seguir las instrucciones para desactivarlo temporalmente

3. **Seleccionar componentes**
   ```
   ✓ Apache
   ✓ MySQL
   ✓ PHP
   ✓ phpMyAdmin
   ✓ Perl (opcional)
   □ FileZilla FTP Server (opcional)
   □ Mercury Mail Server (opcional)
   □ Tomcat (opcional)
   ```

4. **Elegir carpeta de instalación**
   - Por defecto: `C:\xampp`
   - Recomendado: Mantener la ruta por defecto
   - Evitar rutas con espacios o caracteres especiales

5. **Completar la instalación**
   - Clic en "Next" hasta finalizar
   - Marcar "Do you want to start the Control Panel now?"
   - Clic en "Finish"

### 3. Configurar el Panel de Control

1. **Iniciar XAMPP Control Panel**
   - Buscar "XAMPP Control Panel" en el menú inicio
   - O ejecutar: `C:\xampp\xampp-control.exe`

2. **Configurar idioma (opcional)**
   - Clic en "Config" (botón superior derecho)
   - Seleccionar idioma deseado

3. **Iniciar servicios**
   - Clic en "Start" junto a **Apache**
   - Clic en "Start" junto a **MySQL**
   - Los botones se pondrán verdes cuando estén activos

4. **Configurar servicios como servicio de Windows (opcional)**
   - Marca las casillas junto a Apache y MySQL
   - Los servicios se iniciarán automáticamente con Windows

### 4. Verificar la Instalación

1. **Abrir navegador**
   - Ir a: `http://localhost`
   - Deberías ver la página de bienvenida de XAMPP

2. **Verificar phpMyAdmin**
   - Ir a: `http://localhost/phpmyadmin`
   - Interfaz de gestión de MySQL

3. **Verificar PHP**
   - Crear archivo: `C:\xampp\htdocs\info.php`
   - Contenido:
   ```php
   <?php
   phpinfo();
   ?>
   ```
   - Abrir en navegador: `http://localhost/info.php`

### 5. Configurar el Firewall de Windows

1. **Permitir Apache**
   - Windows Defender Firewall pedirá permiso
   - Seleccionar "Redes privadas"
   - Clic en "Permitir acceso"

2. **Configuración manual (si es necesario)**
   - Panel de Control → Sistema y Seguridad → Firewall de Windows
   - "Permitir una aplicación a través de Firewall de Windows"
   - Buscar `C:\xampp\apache\bin\httpd.exe`
   - Marcar "Privada" y "Pública"

### 6. Solucionar Conflictos de Puertos

**Si Apache no inicia (Puerto 80 ocupado):**

1. **Identificar qué usa el puerto 80**
   - Abrir CMD como administrador
   ```cmd
   netstat -ano | findstr :80
   ```

2. **Cambiar puerto de Apache**
   - En XAMPP Control Panel, clic en "Config" junto a Apache
   - Seleccionar "httpd.conf"
   - Buscar: `Listen 80`
   - Cambiar a: `Listen 8080`
   - Buscar: `ServerName localhost:80`
   - Cambiar a: `ServerName localhost:8080`
   - Guardar y reiniciar Apache
   - Acceder: `http://localhost:8080`

**Si MySQL no inicia (Puerto 3306 ocupado):**

1. **Cambiar puerto de MySQL**
   - En XAMPP Control Panel, clic en "Config" junto a MySQL
   - Seleccionar "my.ini"
   - Buscar: `port=3306`
   - Cambiar a: `port=3307`
   - Guardar y reiniciar MySQL

---

## 🐧 Instalación en Linux (LAMP Stack Nativo) {#linux}

### 1. Actualizar el Sistema

```bash
# Ubuntu/Debian
sudo apt update
sudo apt upgrade -y

# Fedora/RHEL/CentOS
sudo dnf update -y

# Arch Linux
sudo pacman -Syu
```

### 2. Instalar Apache

```bash
# Ubuntu/Debian
sudo apt install apache2 -y

# Fedora/RHEL/CentOS
sudo dnf install httpd -y

# Arch Linux
sudo pacman -S apache
```

**Iniciar y habilitar Apache:**

```bash
# Ubuntu/Debian
sudo systemctl start apache2
sudo systemctl enable apache2
sudo systemctl status apache2

# Fedora/RHEL/CentOS/Arch
sudo systemctl start httpd
sudo systemctl enable httpd
sudo systemctl status httpd
```

**Verificar instalación:**
```bash
# Abrir navegador en:
http://localhost
# O desde terminal:
curl http://localhost
```

### 3. Instalar MySQL/MariaDB

**Opción A: MySQL**
```bash
# Ubuntu/Debian
sudo apt install mysql-server -y

# Fedora/RHEL/CentOS
sudo dnf install mysql-server -y

# Arch Linux
sudo pacman -S mysql
```

**Opción B: MariaDB (recomendado)**
```bash
# Ubuntu/Debian
sudo apt install mariadb-server mariadb-client -y

# Fedora/RHEL/CentOS
sudo dnf install mariadb-server mariadb -y

# Arch Linux
sudo pacman -S mariadb
```

**Iniciar y habilitar MySQL/MariaDB:**

```bash
# Para MySQL
sudo systemctl start mysql
sudo systemctl enable mysql
sudo systemctl status mysql

# Para MariaDB
sudo systemctl start mariadb
sudo systemctl enable mariadb
sudo systemctl status mariadb
```

**Configurar seguridad de MySQL/MariaDB:**

```bash
sudo mysql_secure_installation
```

Respuestas recomendadas:
- Enter current password: **[Enter]** (vacío la primera vez)
- Switch to unix_socket authentication: **N**
- Change the root password: **Y** (establecer contraseña segura)
- Remove anonymous users: **Y**
- Disallow root login remotely: **Y**
- Remove test database: **Y**
- Reload privilege tables: **Y**

### 4. Instalar PHP

```bash
# Ubuntu/Debian - PHP 8.x
sudo apt install php libapache2-mod-php php-mysql php-cli php-common php-mbstring php-xml php-curl php-gd php-zip php-json -y

# Fedora/RHEL/CentOS
sudo dnf install php php-mysqlnd php-cli php-common php-mbstring php-xml php-curl php-gd php-zip php-json -y

# Arch Linux
sudo pacman -S php php-apache php-gd php-mbstring
```

**Extensiones adicionales útiles:**
```bash
# Ubuntu/Debian
sudo apt install php-intl php-soap php-bcmath php-opcache php-apcu -y

# Fedora/RHEL/CentOS
sudo dnf install php-intl php-soap php-bcmath php-opcache php-apcu -y

# Arch Linux
sudo pacman -S php-intl php-sodium
```

### 5. Configurar Apache para PHP

**Ubuntu/Debian:**
```bash
# Habilitar módulo PHP
sudo a2enmod php8.1  # O la versión instalada (php8.2, php8.3, etc.)

# Configurar DirectoryIndex
sudo nano /etc/apache2/mods-enabled/dir.conf
```

Modificar para que index.php sea prioritario:
```apache
<IfModule mod_dir.c>
    DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
</IfModule>
```

```bash
# Reiniciar Apache
sudo systemctl restart apache2
```

**Fedora/RHEL/CentOS:**
```bash
# Editar configuración de PHP
sudo nano /etc/httpd/conf.d/php.conf

# Reiniciar Apache
sudo systemctl restart httpd
```

**Arch Linux:**
```bash
# Editar httpd.conf
sudo nano /etc/httpd/conf/httpd.conf

# Comentar la línea:
#LoadModule mpm_event_module modules/mod_mpm_event.so

# Descomentar:
LoadModule mpm_prefork_module modules/mod_mpm_prefork.so

# Agregar al final:
LoadModule php_module modules/libphp.so
AddHandler php-script .php
Include conf/extra/php_module.conf

# Crear archivo de configuración PHP
sudo nano /etc/httpd/conf/extra/php_module.conf
```

Contenido:
```apache
<FilesMatch \.php$>
    SetHandler application/x-httpd-php
</FilesMatch>

DirectoryIndex index.php index.html
```

```bash
# Reiniciar Apache
sudo systemctl restart httpd
```

### 6. Instalar phpMyAdmin

```bash
# Ubuntu/Debian
sudo apt install phpmyadmin -y

# Durante la instalación:
# - Servidor web: [*] apache2
# - Configurar base de datos: Yes
# - Contraseña de phpMyAdmin: [ingresar contraseña]

# Habilitar configuración de phpMyAdmin
sudo ln -s /usr/share/phpmyadmin /var/www/html/phpmyadmin

# Fedora/RHEL/CentOS
sudo dnf install phpmyadmin -y

# Arch Linux
sudo pacman -S phpmyadmin
```

**Configurar phpMyAdmin manualmente (si es necesario):**

```bash
# Crear archivo de configuración para Apache
sudo nano /etc/apache2/conf-available/phpmyadmin.conf
```

Contenido:
```apache
Alias /phpmyadmin /usr/share/phpmyadmin

<Directory /usr/share/phpmyadmin>
    Options SymLinksIfOwnerMatch
    DirectoryIndex index.php
    AllowOverride All
    Require all granted
</Directory>
```

```bash
# Habilitar configuración
sudo a2enconf phpmyadmin

# Reiniciar Apache
sudo systemctl restart apache2
```

**Acceder a phpMyAdmin:**
```
http://localhost/phpmyadmin
```

### 7. Comandos Básicos de Gestión

**Apache:**
```bash
# Ubuntu/Debian
sudo systemctl start apache2      # Iniciar
sudo systemctl stop apache2       # Detener
sudo systemctl restart apache2    # Reiniciar
sudo systemctl reload apache2     # Recargar configuración
sudo systemctl status apache2     # Ver estado

# Fedora/RHEL/CentOS/Arch
sudo systemctl start httpd
sudo systemctl stop httpd
sudo systemctl restart httpd
sudo systemctl reload httpd
sudo systemctl status httpd
```

**MySQL/MariaDB:**
```bash
sudo systemctl start mysql        # o mariadb
sudo systemctl stop mysql
sudo systemctl restart mysql
sudo systemctl status mysql
```

**PHP:**
```bash
# Ver versión
php -v

# Ver módulos instalados
php -m

# Ver configuración
php -i

# Probar sintaxis de archivo
php -l archivo.php

# Ejecutar script
php archivo.php
```

### 8. Crear Alias para Facilitar el Uso

```bash
# Editar .bashrc
nano ~/.bashrc

# Agregar al final:
# Para Ubuntu/Debian
alias apache-start='sudo systemctl start apache2'
alias apache-stop='sudo systemctl stop apache2'
alias apache-restart='sudo systemctl restart apache2'
alias apache-status='sudo systemctl status apache2'

# Para Fedora/RHEL/CentOS/Arch (cambiar apache2 por httpd)
alias apache-start='sudo systemctl start httpd'
alias apache-stop='sudo systemctl stop httpd'
alias apache-restart='sudo systemctl restart httpd'
alias apache-status='sudo systemctl status httpd'

# MySQL/MariaDB
alias mysql-start='sudo systemctl start mysql'
alias mysql-stop='sudo systemctl stop mysql'
alias mysql-restart='sudo systemctl restart mysql'
alias mysql-status='sudo systemctl status mysql'

# Logs
alias apache-log='sudo tail -f /var/log/apache2/error.log'
alias mysql-log='sudo tail -f /var/log/mysql/error.log'

# Guardar y recargar
source ~/.bashrc
```

### 9. Estructura de Directorios

**Directorio raíz web (DocumentRoot):**
```bash
# Ubuntu/Debian
/var/www/html/

# Fedora/RHEL/CentOS
/var/www/html/

# Arch Linux
/srv/http/
```

**Archivos de configuración:**
```bash
# Apache
# Ubuntu/Debian: /etc/apache2/
# Fedora/RHEL/CentOS/Arch: /etc/httpd/

# PHP
# Ubuntu/Debian: /etc/php/8.x/apache2/php.ini
# Fedora/RHEL/CentOS: /etc/php.ini
# Arch Linux: /etc/php/php.ini

# MySQL/MariaDB
# Ubuntu/Debian: /etc/mysql/
# Fedora/RHEL/CentOS: /etc/my.cnf
# Arch Linux: /etc/my.cnf
```

**Logs:**
```bash
# Apache
# Ubuntu/Debian: /var/log/apache2/
# Fedora/RHEL/CentOS/Arch: /var/log/httpd/

# MySQL/MariaDB
# Ubuntu/Debian: /var/log/mysql/
# Fedora/RHEL/CentOS/Arch: /var/log/mariadb/
```

### 10. Configurar Permisos del Directorio Web

```bash
# Ubuntu/Debian
sudo chown -R $USER:www-data /var/www/html
sudo chmod -R 755 /var/www/html

# Fedora/RHEL/CentOS
sudo chown -R $USER:apache /var/www/html
sudo chmod -R 755 /var/www/html

# Arch Linux
sudo chown -R $USER:http /srv/http
sudo chmod -R 755 /srv/http

# Verificar usuario actual
whoami
```

**Permisos recomendados para desarrollo:**
```bash
# Agregar tu usuario al grupo del servidor web
# Ubuntu/Debian
sudo usermod -a -G www-data $USER

# Fedora/RHEL/CentOS
sudo usermod -a -G apache $USER

# Arch Linux
sudo usermod -a -G http $USER

# Cerrar sesión y volver a iniciar para aplicar cambios
```

### 11. Verificar Instalación Completa

**Crear archivo de prueba PHP:**

```bash
# Ubuntu/Debian
sudo nano /var/www/html/info.php

# Fedora/RHEL/CentOS
sudo nano /var/www/html/info.php

# Arch Linux
sudo nano /srv/http/info.php
```

Contenido:
```php
<?php
phpinfo();
?>
```

**Verificar en navegador:**
```
http://localhost/info.php
```

**Crear prueba de conexión MySQL:**

```bash
# Ubuntu/Debian
nano /var/www/html/test_mysql.php

# Fedora/RHEL/CentOS
nano /var/www/html/test_mysql.php

# Arch Linux
nano /srv/http/test_mysql.php
```

Contenido:
```php
<?php
$servidor = "localhost";
$usuario = "root";
$password = "tu_contraseña";

$conn = new mysqli($servidor, $usuario, $password);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

echo "<h2>✓ Conexión exitosa a MySQL/MariaDB</h2>";
echo "<p>Versión: " . $conn->server_info . "</p>";

$conn->close();
?>
```

**Verificar:**
```
http://localhost/test_mysql.php
```

### 12. Configurar Firewall

```bash
# Ubuntu/Debian (UFW)
sudo ufw allow 'Apache Full'
sudo ufw allow 3306/tcp
sudo ufw enable
sudo ufw status

# Fedora/RHEL/CentOS (Firewalld)
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --permanent --add-port=3306/tcp
sudo firewall-cmd --reload

# Verificar
sudo firewall-cmd --list-all
```

### 13. Habilitar mod_rewrite (URLs amigables)

```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2

# Fedora/RHEL/CentOS - ya está habilitado por defecto

# Arch Linux
# Editar httpd.conf y descomentar:
sudo nano /etc/httpd/conf/httpd.conf
# LoadModule rewrite_module modules/mod_rewrite.so
```

**Permitir .htaccess:**

```bash
# Ubuntu/Debian
sudo nano /etc/apache2/apache2.conf
```

Buscar `<Directory /var/www/>` y cambiar:
```apache
<Directory /var/www/>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

```bash
# Fedora/RHEL/CentOS
sudo nano /etc/httpd/conf/httpd.conf
```

Buscar `<Directory "/var/www/html">` y cambiar:
```apache
<Directory "/var/www/html">
    AllowOverride All
</Directory>
```

```bash
# Reiniciar Apache
sudo systemctl restart apache2  # o httpd
```

### 14. Configurar SELinux (Fedora/RHEL/CentOS)

```bash
# Ver estado de SELinux
sestatus

# Permitir a Apache conectarse a la red
sudo setsebool -P httpd_can_network_connect 1

# Permitir a Apache conectarse a bases de datos
sudo setsebool -P httpd_can_network_connect_db 1

# Dar contexto correcto a directorios
sudo chcon -R -t httpd_sys_content_t /var/www/html
sudo chcon -R -t httpd_sys_rw_content_t /var/www/html

# Si quieres desactivar SELinux temporalmente (no recomendado en producción)
sudo setenforce 0
```

---

## ⚙️ Configuración Inicial {#configuración-inicial}

### 1. Configurar Seguridad de MySQL

**Windows:**
```cmd
cd C:\xampp\mysql\bin
mysql -u root
```

**Linux:**
```bash
cd /opt/lampp/bin
./mysql -u root
```

**Establecer contraseña de root:**
```sql
ALTER USER 'root'@'localhost' IDENTIFIED BY 'tu_contraseña_segura';
FLUSH PRIVILEGES;
EXIT;
```

### 2. Configurar phpMyAdmin con Contraseña

**Windows:** `C:\xampp\phpMyAdmin\config.inc.php`
**Linux:** `/opt/lampp/phpmyadmin/config.inc.php`

```php
<?php
// Buscar esta línea:
$cfg['Servers'][$i]['auth_type'] = 'config';

// Cambiar a:
$cfg['Servers'][$i]['auth_type'] = 'cookie';

// Buscar:
$cfg['Servers'][$i]['password'] = '';

// Cambiar a:
$cfg['Servers'][$i]['password'] = 'tu_contraseña_segura';
?>
```

### 3. Configurar Blowfish Secret

En el mismo archivo `config.inc.php`:
```php
<?php
$cfg['blowfish_secret'] = 'tu_cadena_aleatoria_de_32_caracteres_minimo_aqui';
?>
```

Generar cadena aleatoria:
```bash
# En Linux
openssl rand -base64 32

# En Windows (PowerShell)
-join ((48..57) + (65..90) + (97..122) | Get-Random -Count 32 | % {[char]$_})
```

---

## 🚀 Primeros Pasos {#primeros-pasos}

### 1. Estructura de Carpetas

**Windows:**
```
C:\xampp\
├── apache\          # Servidor Apache
├── mysql\           # Base de datos MySQL
├── php\             # Intérprete PHP
├── htdocs\          # 📁 Aquí van tus proyectos web
├── phpMyAdmin\      # Administrador de BD
└── ...
```

**Linux:**
```
/var/www/html/       # 📁 Ubuntu/Debian/Fedora - Aquí van tus proyectos web
/srv/http/           # 📁 Arch Linux - Aquí van tus proyectos web

Configuración:
/etc/apache2/        # Ubuntu/Debian - Configuración Apache
/etc/httpd/          # Fedora/RHEL/CentOS/Arch - Configuración Apache
/etc/php/            # Ubuntu/Debian - Configuración PHP
/etc/php.ini         # Fedora/RHEL/CentOS/Arch - Configuración PHP
/etc/mysql/          # Configuración MySQL/MariaDB
```

### 2. Crear tu Primer Proyecto

**Windows:**
```cmd
cd C:\xampp\htdocs
mkdir mi_proyecto
cd mi_proyecto
```

**Linux (Ubuntu/Debian/Fedora/RHEL/CentOS):**
```bash
cd /var/www/html
mkdir mi_proyecto
cd mi_proyecto
```

**Linux (Arch):**
```bash
cd /srv/http
mkdir mi_proyecto
cd mi_proyecto
```

### 3. Crear archivo index.php

```php
<?php
// index.php
echo "<h1>¡Hola Mundo desde PHP!</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
?>
```

### 4. Probar Conexión a MySQL

```php
<?php
// test_mysql.php
$servidor = "localhost";
$usuario = "root";
$password = ""; // o tu contraseña si la configuraste
$base_datos = "test";

// Crear conexión
$conn = new mysqli($servidor, $usuario, $password);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

echo "<h2>✓ Conexión exitosa a MySQL</h2>";
echo "<p>Versión de MySQL: " . $conn->server_info . "</p>";

$conn->close();
?>
```

Acceder:
- Windows: `http://localhost/mi_proyecto/test_mysql.php`
- Linux: `http://localhost/mi_proyecto/test_mysql.php`

### 5. Crear Base de Datos de Prueba

**Desde phpMyAdmin:**
1. Ir a `http://localhost/phpmyadmin`
2. Clic en "Nueva" o "New"
3. Nombre: `mi_base_datos`
4. Cotejamiento: `utf8mb4_unicode_ci`
5. Clic en "Crear"

**Desde terminal/cmd:**

**Windows:**
```cmd
cd C:\xampp\mysql\bin
mysql -u root -p
```

**Linux:**
```bash
# Conectar a MySQL/MariaDB
mysql -u root -p

# O si configuraste contraseña durante mysql_secure_installation
sudo mysql -u root -p
```

**Comandos SQL:**
```sql
-- Crear base de datos
CREATE DATABASE mi_base_datos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE mi_base_datos;

-- Crear tabla de ejemplo
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    edad INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar datos de ejemplo
INSERT INTO usuarios (nombre, email, edad) VALUES
('Juan Pérez', 'juan@example.com', 25),
('María García', 'maria@example.com', 30),
('Carlos López', 'carlos@example.com', 28);

-- Ver los datos
SELECT * FROM usuarios;
```

### 6. Script PHP Completo de Conexión

```php
<?php
// conexion.php
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "mi_base_datos";

// Crear conexión
$conn = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer charset
$conn->set_charset("utf8mb4");

echo "<h2>Conexión exitosa</h2>";

// Consultar datos
$sql = "SELECT id, nombre, email, edad FROM usuarios";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Edad</th></tr>";
    
    while($fila = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $fila["id"] . "</td>";
        echo "<td>" . $fila["nombre"] . "</td>";
        echo "<td>" . $fila["email"] . "</td>";
        echo "<td>" . $fila["edad"] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No hay usuarios registrados</p>";
}

$conn->close();
?>
```

---

## 🔧 Configuración de PHP {#configuración-php}

### Ubicación del archivo php.ini

**Windows:** `C:\xampp\php\php.ini`

**Linux:**
- Ubuntu/Debian: `/etc/php/8.x/apache2/php.ini` (reemplazar 8.x con tu versión)
- Fedora/RHEL/CentOS: `/etc/php.ini`
- Arch Linux: `/etc/php/php.ini`

**Encontrar ubicación exacta:**
```bash
# Desde terminal
php --ini

# O crear archivo PHP con:
<?php echo php_ini_loaded_file(); ?>
```

### Configuraciones Importantes

**1. Límite de Tiempo de Ejecución:**
```ini
max_execution_time = 300
```

**2. Tamaño de Subida de Archivos:**
```ini
upload_max_filesize = 50M
post_max_size = 50M
```

**3. Límite de Memoria:**
```ini
memory_limit = 256M
```

**4. Mostrar Errores (solo desarrollo):**
```ini
display_errors = On
display_startup_errors = On
error_reporting = E_ALL
```

**5. Zona Horaria:**
```ini
date.timezone = Europe/Madrid
; Otras opciones:
; America/Mexico_City
; America/Argentina/Buenos_Aires
; America/Bogota
```

**6. Extensiones Necesarias (descomentar o verificar):**

**En php.ini, buscar y descomentar (quitar el `;` al inicio):**
```ini
extension=mysqli
extension=pdo_mysql
extension=mbstring
extension=openssl
extension=curl
extension=gd
extension=fileinfo
extension=intl
extension=zip
```

**En Linux, las extensiones se instalan como paquetes separados:**
```bash
# Ubuntu/Debian
sudo apt install php-mysqli php-mbstring php-curl php-gd php-xml php-zip php-intl -y

# Fedora/RHEL/CentOS
sudo dnf install php-mysqlnd php-mbstring php-curl php-gd php-xml php-zip php-intl -y

# Arch Linux
sudo pacman -S php-gd php-intl php-sodium

# Reiniciar Apache después de instalar extensiones
sudo systemctl restart apache2  # o httpd
```

### Verificar Configuración de PHP

```php
<?php
// phpinfo.php
phpinfo();
?>
```

### Cambiar Versión de PHP (XAMPP Windows)

1. Descargar versión deseada de PHP desde [php.net](https://windows.php.net/download/)
2. Extraer en `C:\xampp\php_versions\php-8.x.x`
3. Renombrar carpeta actual:
   ```cmd
   cd C:\xampp
   ren php php_old
   ```
4. Crear enlace simbólico o copiar nueva versión:
   ```cmd
   mklink /D php C:\xampp\php_versions\php-8.x.x
   ```
5. Reiniciar Apache

---

## 🗄️ Configuración de MySQL {#configuración-mysql}

### Ubicación del archivo my.ini/my.cnf

**Windows:** `C:\xampp\mysql\bin\my.ini`

**Linux:**
- Ubuntu/Debian: `/etc/mysql/my.cnf` o `/etc/mysql/mysql.conf.d/mysqld.cnf`
- Fedora/RHEL/CentOS: `/etc/my.cnf`
- Arch Linux: `/etc/my.cnf`

**Encontrar ubicación:**
```bash
mysql --help | grep "Default options" -A 1
```

### Configuraciones Importantes

**1. Puerto de MySQL:**
```ini
[mysqld]
port=3306
```

**2. Charset por Defecto:**
```ini
[mysqld]
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci

[client]
default-character-set=utf8mb4
```

**3. Límites de Conexión:**
```ini
[mysqld]
max_connections=100
max_allowed_packet=16M
```

**4. Modo SQL:**
```ini
[mysqld]
sql_mode=NO_ENGINE_SUBSTITUTION
```

### Comandos Útiles de MySQL

**Conectar a MySQL:**

**Windows:**
```cmd
cd C:\xampp\mysql\bin
mysql -u root -p
```

**Linux:**
```bash
# Simplemente:
mysql -u root -p

# O con sudo si es necesario:
sudo mysql -u root -p
```

**Comandos básicos:**
```sql
-- Ver bases de datos
SHOW DATABASES;

-- Crear base de datos
CREATE DATABASE nombre_bd;

-- Usar base de datos
USE nombre_bd;

-- Ver tablas
SHOW TABLES;

-- Ver estructura de tabla
DESCRIBE nombre_tabla;

-- Ver usuarios
SELECT user, host FROM mysql.user;

-- Crear usuario
CREATE USER 'nuevo_usuario'@'localhost' IDENTIFIED BY 'contraseña';

-- Dar permisos
GRANT ALL PRIVILEGES ON nombre_bd.* TO 'nuevo_usuario'@'localhost';
FLUSH PRIVILEGES;

-- Ver versión
SELECT VERSION();

-- Ver charset
SHOW VARIABLES LIKE 'character_set%';

-- Salir
EXIT;
```

### Backup y Restore

**Hacer Backup:**

**Windows:**
```cmd
cd C:\xampp\mysql\bin
mysqldump -u root -p nombre_bd > C:\backups\backup.sql
```

**Linux:**
```bash
# Hacer backup
mysqldump -u root -p nombre_bd > /home/usuario/backups/backup.sql

# Con fecha en el nombre
mysqldump -u root -p nombre_bd > /home/usuario/backups/backup_$(date +%Y%m%d).sql
```

**Restaurar Backup:**

**Windows:**
```cmd
cd C:\xampp\mysql\bin
mysql -u root -p nombre_bd < C:\backups\backup.sql
```

**Linux:**
```bash
# Restaurar backup
mysql -u root -p nombre_bd < /home/usuario/backups/backup.sql
```

---

## ❗ Problemas Comunes {#problemas-comunes}

### Windows

**1. Apache no inicia - Puerto 80 ocupado**

**Solución 1: Cambiar puerto**
```ini
# C:\xampp\apache\conf\httpd.conf
Listen 8080
ServerName localhost:8080

# C:\xampp\apache\conf\extra\httpd-ssl.conf
Listen 4433
<VirtualHost _default_:4433>
```

**Solución 2: Encontrar y cerrar aplicación**
```cmd
# Ver qué usa el puerto 80
netstat -ano | findstr :80

# Matar proceso (PID que aparece al final)
taskkill /PID numero_pid /F
```

**Solución 3: Desactivar Skype**
- Skype → Herramientas → Opciones → Avanzadas → Conexión
- Desmarcar "Usar puerto 80 y 443"

**2. MySQL no inicia - Puerto 3306 ocupado**
```cmd
# Ver qué usa el puerto 3306
netstat -ano | findstr :3306

# Cambiar puerto en: C:\xampp\mysql\bin\my.ini
port=3307

# También en: C:\xampp\phpMyAdmin\config.inc.php
$cfg['Servers'][$i]['port'] = '3307';
```

**3. Error: Missing MSVCR110.dll**
- Descargar: Microsoft Visual C++ 2012 Redistributable
- Instalar y reiniciar

**4. Acceso denegado a phpMyAdmin**
```php
// C:\xampp\phpMyAdmin\config.inc.php
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = '';
$cfg['Servers'][$i]['AllowNoPassword'] = true;
```

### Linux

**1. Apache no inicia - Puerto 80 ocupado**

```bash
# Ver qué proceso usa el puerto 80
sudo netstat -tlnp | grep :80
# O
sudo lsof -i :80

# Si es Apache del sistema:
sudo systemctl stop apache2
sudo systemctl disable apache2

# Si es nginx:
sudo systemctl stop nginx
sudo systemctl disable nginx

# Cambiar puerto de Apache (si prefieres usar otro puerto):
# Ubuntu/Debian
sudo nano /etc/apache2/ports.conf
# Cambiar: Listen 80 → Listen 8080

sudo nano /etc/apache2/sites-available/000-default.conf
# Cambiar: <VirtualHost *:80> → <VirtualHost *:8080>

# Fedora/RHEL/CentOS/Arch
sudo nano /etc/httpd/conf/httpd.conf
# Cambiar: Listen 80 → Listen 8080

# Reiniciar
sudo systemctl restart apache2  # o httpd
```

**2. MySQL/MariaDB no inicia - Puerto 3306 ocupado**

```bash
# Ver qué usa el puerto
sudo netstat -tlnp | grep :3306

# Si es MySQL/MariaDB del sistema en conflicto
sudo systemctl stop mysql
sudo systemctl disable mysql

# Cambiar puerto MySQL
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf  # Ubuntu/Debian
sudo nano /etc/my.cnf  # Fedora/RHEL/CentOS/Arch

# Buscar y cambiar:
[mysqld]
port=3307

# También actualizar en phpMyAdmin
sudo nano /etc/phpmyadmin/config.inc.php
$cfg['Servers'][$i]['port'] = '3307';

# Reiniciar
sudo systemctl restart mysql  # o mariadb
```

**3. Permisos denegados en /var/www/html**

```bash
# Cambiar propietario
sudo chown -R $USER:www-data /var/www/html  # Ubuntu/Debian
sudo chown -R $USER:apache /var/www/html     # Fedora/RHEL/CentOS
sudo chown -R $USER:http /srv/http           # Arch Linux

# Establecer permisos correctos
sudo chmod -R 755 /var/www/html

# Agregar tu usuario al grupo del servidor web
sudo usermod -a -G www-data $USER   # Ubuntu/Debian
sudo usermod -a -G apache $USER     # Fedora/RHEL/CentOS
sudo usermod -a -G http $USER       # Arch Linux

# Cerrar sesión y volver a entrar
```

**4. Error: "Forbidden - You don't have permission"**

```bash
# Ubuntu/Debian
sudo nano /etc/apache2/apache2.conf

# Buscar y modificar:
<Directory /var/www/>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>

# Fedora/RHEL/CentOS/Arch
sudo nano /etc/httpd/conf/httpd.conf

<Directory "/var/www/html">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>

# Reiniciar Apache
sudo systemctl restart apache2  # o httpd
```

**5. SELinux bloquea Apache (Fedora/RHEL/CentOS)**

```bash
# Ver estado
sestatus

# Permitir conexiones de red
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_can_network_connect_db 1

# Dar contexto correcto
sudo chcon -R -t httpd_sys_content_t /var/www/html
sudo chcon -R -t httpd_sys_rw_content_t /var/www/html

# Verificar logs de SELinux
sudo tail -f /var/log/audit/audit.log

# Si todo falla (solo para pruebas):
sudo setenforce 0  # Desactivar temporalmente
```

**6. phpMyAdmin no carga o da error 404**

```bash
# Ubuntu/Debian - Verificar instalación
sudo apt install phpmyadmin

# Crear enlace simbólico
sudo ln -s /usr/share/phpmyadmin /var/www/html/phpmyadmin

# Fedora/RHEL/CentOS
sudo dnf install phpmyadmin

# Configurar acceso en Apache
sudo nano /etc/httpd/conf.d/phpMyAdmin.conf

# Agregar:
Alias /phpmyadmin /usr/share/phpMyAdmin
<Directory /usr/share/phpMyAdmin/>
   AddDefaultCharset UTF-8
   Require all granted
</Directory>

# Reiniciar Apache
sudo systemctl restart apache2  # o httpd
```

**7. MySQL/MariaDB no acepta conexiones desde PHP**

```bash
# Editar configuración
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf  # Ubuntu/Debian
sudo nano /etc/my.cnf  # Otros

# Comentar la línea (agregar # al inicio):
# bind-address = 127.0.0.1

# O cambiar a:
bind-address = 0.0.0.0

# Reiniciar
sudo systemctl restart mysql  # o mariadb

# Verificar puerto
sudo netstat -tlnp | grep mysql
```

**8. Extensión PHP no carga**

```bash
# Ver extensiones cargadas
php -m

# Instalar extensión faltante
# Ubuntu/Debian
sudo apt install php-nombre_extension

# Fedora/RHEL/CentOS
sudo dnf install php-nombre_extension

# Arch Linux
sudo pacman -S php-nombre_extension

# Reiniciar Apache
sudo systemctl restart apache2  # o httpd

# Verificar logs de errores
sudo tail -f /var/log/apache2/error.log  # Ubuntu/Debian
sudo tail -f /var/log/httpd/error_log    # Fedora/RHEL/CentOS/Arch
```

**9. Error: "Cannot connect to database server"**

```bash
# Verificar que MySQL esté corriendo
sudo systemctl status mysql  # o mariadb

# Iniciar si está detenido
sudo systemctl start mysql

# Probar conexión desde terminal
mysql -u root -p

# Verificar permisos de usuario
mysql -u root -p
```
```sql
SELECT user, host FROM mysql.user;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY 'tu_contraseña';
FLUSH PRIVILEGES;
```

**10. Apache muestra código PHP en lugar de ejecutarlo**

```bash
# Verificar que el módulo PHP esté cargado
# Ubuntu/Debian
sudo a2enmod php8.1  # o tu versión
sudo systemctl restart apache2

# Fedora/RHEL/CentOS/Arch
sudo nano /etc/httpd/conf/httpd.conf

# Verificar que existan estas líneas:
LoadModule php_module modules/libphp.so
AddHandler php-script .php
<FilesMatch \.php$>
    SetHandler application/x-httpd-php
</FilesMatch>

# Reiniciar
sudo systemctl restart httpd

# Verificar que PHP está instalado
php -v
```

### Problemas Comunes en Ambos Sistemas

**1. Error: Forbidden - You don't have permission**
```apache
# En httpd.conf (buscar Directory "htdocs")
<Directory "/ruta/htdocs">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

**2. PHP no se ejecuta - se descarga**
```apache
# Verificar en httpd.conf:
LoadModule php_module modules/libphp.so
AddType application/x-httpd-php .php
```

**3. Error: mysqli no encontrado**
```ini
# php.ini - descomentar:
extension=mysqli
extension=pdo_mysql

# Reiniciar Apache
```

**4. Caracteres extraños (ñ, tildes)**
```php
<?php
// Al inicio de conexión
$conn->set_charset("utf8mb4");

// En HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
```

---

## 🛠️ Herramientas Útiles {#herramientas-útiles}

### 1. Virtual Hosts (Dominios Locales)

**Configurar Virtual Hosts:**

**Windows:** `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
**Linux:** `/opt/lampp/etc/extra/httpd-vhosts.conf`

```apache
# Habilitar en httpd.conf (descomentar):
Include etc/extra/httpd-vhosts.conf

# Agregar en httpd-vhosts.conf:
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs"
    ServerName localhost
</VirtualHost>

<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/mi_proyecto"
    ServerName miproyecto.local
    <Directory "C:/xampp/htdocs/mi_proyecto">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Editar archivo hosts:**

**Windows:** `C:\Windows\System32\drivers\etc\hosts` (como administrador)
**Linux:** `/etc/hosts` (con sudo)

```
127.0.0.1 localhost
127.0.0.1 miproyecto.local
```

Acceder: `http://miproyecto.local`

### 2. Habilitar mod_rewrite (URLs amigables)

```apache
# httpd.conf - descomentar:
LoadModule rewrite_module modules/mod_rewrite.so

# Verificar en Directory:
<Directory "/xampp/htdocs">
    AllowOverride All
</Directory>
```

**Ejemplo .htaccess:**
```apache
# .htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

### 3. Habilitar SSL/HTTPS

```apache
# httpd.conf - descomentar:
LoadModule ssl_module modules/mod_ssl.so
Include etc/extra/httpd-ssl.conf

# Reiniciar Apache
```

Acceder: `https://localhost`

### 4. Logs de Errores

**Windows:**
- Apache: `C:\xampp\apache\logs\error.log`
- PHP: `C:\xampp\php\logs\php_error_log`
- MySQL: `C:\xampp\mysql\data\mysql_error.log`

**Linux:**
- Apache: `/opt/lampp/logs/error_log`
- PHP: `/opt/lampp/logs/php_error_log`
- MySQL: `/opt/lampp/var/mysql/mysql_error.log`

**Ver logs en tiempo real:**

**Windows:**
```cmd
type C:\xampp\apache\logs\error.log
```

**Linux:**
```bash
tail -f /opt/lampp/logs/error_log
```

### 5. Herramientas de Desarrollo

**Extensiones de VSCode útiles:**
- PHP Intelephense
- PHP Debug
- MySQL (cweijan.vscode-mysql-client2)
- Apache Conf
- PHPDoc Comment

**Configurar VSCode con XAMPP:**
```json
{
    "php.validate.executablePath": "C:/xampp/php/php.exe",
    "php.suggest.basic": true
}
```

### 6. Comandos Útiles

**Ver versiones:**
```bash
# PHP
php -v

# MySQL
mysql --version

# Apache (Linux)
/opt/lampp/bin/apachectl -v
```

**Ver extensiones PHP:**
```bash
php -m
```

**Verificar sintaxis PHP:**
```bash
php -l archivo.php
```

**Ejecutar PHP desde terminal:**
```bash
php -r "echo 'Hola Mundo';"
```

---

## 📋 Checklist Final

### ✅ Instalación Completa

- [ ] XAMPP instalado correctamente
- [ ] Apache inicia sin errores
- [ ] MySQL inicia sin errores
- [ ] `http://localhost` muestra página de XAMPP
- [ ] `http://localhost/phpmyadmin` funciona
- [ ] PHP ejecuta scripts correctamente
- [ ] Conexión a MySQL funciona

### ✅ Seguridad Básica

- [ ] Contraseña establecida para root de MySQL
- [ ] phpMyAdmin protegido con contraseña
- [ ] Firewall configurado
- [ ] Acceso externo des
