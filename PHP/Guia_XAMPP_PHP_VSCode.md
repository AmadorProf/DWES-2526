# Guía para ejecutar y visualizar código PHP con XAMPP y Visual Studio Code

## Requisitos previos
- XAMPP instalado (incluye Apache, PHP y MySQL)
- Visual Studio Code instalado
- Navegador (Chrome, Firefox, Edge, etc.)

---

## Paso a paso

### 1. Ubicación de archivos PHP
Por defecto, XAMPP sirve los archivos desde la carpeta `htdocs`:

- **Windows:** `C:\xampp\htdocs\`

Crea una carpeta para tu proyecto, por ejemplo:
```
C:\xampp\htdocs\mi_proyecto_PHP
```

### 2. Iniciar Apache
- Abre **Panel de Control de XAMPP**
- Pulsa **Start** en *Apache*
- Comprueba en el navegador: `http://localhost/`

> Si Apache no inicia, puede haber un conflicto de puertos. Cambia el puerto 80 a 8080 en `httpd.conf`.

---

### 3. Crear tu primer archivo PHP
Crea `index.php` dentro de tu carpeta del proyecto:

```php
<!DOCTYPE html>
<html>
<head>
    <title>Mi primer programa en PHP</title>
</head>
<body>
    <?php
        print("Hola mundo");
        // o también puedes usar:
        echo "Hola mundo";
    ?>
</body>
</html>
```

Abre en el navegador:
```
http://localhost/mi_proyecto_PHP/index.php
```

---

### 4. Extensiones útiles para VS Code
Instala desde el Marketplace:
- **PHP Intelephense** — autocompletado y validación
- **PHP Debug** (Felix Becker) — depuración con Xdebug
- **PHP DocBlocker**, **PHP Namespace Resolver** *(opcional)*

---

### 5. Configurar PHP en VS Code (opcional)
En *settings.json* añade:
```json
"php.validate.executablePath": "C:\\xampp\\php\\php.exe"
```

---

### 6. Activar Xdebug para depurar
1. Crea un archivo `phpinfo.php` con:
   ```php
   <?php phpinfo();
   ```
   Ábrelo en el navegador y localiza la ruta de `php.ini`.

2. Edita el archivo `php.ini` y agrega al final:
   ```
   [xdebug]
   zend_extension="C:\xampp\php\ext\php_xdebug.dll"
   xdebug.mode=develop,debug
   xdebug.start_with_request=yes
   xdebug.client_host=127.0.0.1
   xdebug.client_port=9003
   xdebug.log="C:\xampp\tmp\xdebug.log"
   ```

3. Reinicia Apache y verifica con `phpinfo()` que Xdebug está activo.

---

### 7. Configurar depuración en VS Code
Crea `.vscode/launch.json` dentro de tu proyecto:

```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Listen for XDebug",
      "type": "php",
      "request": "launch",
      "port": 9003,
      "pathMappings": {
        "/var/www/html": "${workspaceFolder}"
      }
    }
  ]
}
```

---

### 8. Ejecutar depuración
1. Coloca un breakpoint en tu archivo PHP.
2. En VS Code, ve a **Run and Debug → Listen for XDebug → ▶️ Start**.
3. Abre tu archivo PHP en el navegador.
4. VS Code se detendrá en el punto de interrupción.

---

### 9. Ejecutar scripts PHP desde terminal
- **Windows:**
  ```bash
  C:\xampp\php\php.exe C:\xampp\htdocs\mi_proyecto\script.php
  ```
- **macOS/Linux:**
  ```bash
  /opt/lampp/bin/php /opt/lampp/htdocs/mi_proyecto/script.php
  ```

---

## Posibles fallos y soluciones

| Problema | Causa | Solución |
|-----------|--------|----------|
| **Apache no arranca** | Puerto 80 o 443 ocupado | Cambiar puertos en `httpd.conf` y `httpd-ssl.conf` (por ejemplo 8080 / 4443) |
| **PHP no se interpreta (se muestra el código)** | Se abre el archivo directamente con `file://` | Usa `http://localhost/...` |
| **Xdebug no se conecta a VS Code** | Configuración incorrecta o puerto bloqueado | Verificar `xdebug.mode=debug`, `client_port=9003`, y firewall |
| **Xdebug no aparece en phpinfo()** | No se cargó extensión | Revisar ruta `zend_extension` en `php.ini` |
| **Cambios no se reflejan** | Caché o servidor no reiniciado | Reinicia Apache y limpia caché del navegador |
| **Depuración no se detiene en el breakpoint** | VS Code no está escuchando | Asegurarse de tener “Listen for XDebug” activo antes de recargar la página |
| **Permisos denegados (Linux/macOS)** | Archivos no tienen permisos de lectura | Ejecutar `chmod -R 755 /opt/lampp/htdocs/mi_proyecto` |

---

## Resumen rápido
1. Crea proyecto en `htdocs/mi_proyecto`  
2. Abre en VS Code y crea `index.php`  
3. Inicia Apache desde XAMPP  
4. Abre `http://localhost/mi_proyecto`  
5. Configura Xdebug y `launch.json`  
6. Inicia depuración en VS Code

---
