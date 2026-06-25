# Rúbrica de Evaluación - DWES 1ª Evaluación

**Asignatura:** Desarrollo Web en Entorno Servidor  
**Curso:** 2025-26  
**Evaluación:** Primera Evaluación (RA2, RA3, RA4 y RA5)

---

## EJERCICIO 1 (RA2) - 2 puntos

**Aplicación web de aventura interactiva (aventura.php)**

| Criterio | Puntuación | Descripción |
|----------|------------|-------------|
| **Formulario y estructura básica** | 0,5 | Formulario con tres opciones (explorar bosque, luchar contra monstruo, descansar). Estructura HTML correcta y uso del método POST. |
| **Sistema de energía y oro** | 0,4 | Implementación correcta de la mecánica de energía y oro según las reglas: explorar (-10 energía, +10-50 oro), luchar (-10-30 energía, 50% probabilidad de +40 oro), descansar (+20 energía, máximo 100). |
| **Límites y victoria** | 0,4 | Control del límite máximo de energía (100). Detección de victoria al alcanzar 1000 monedas y mostrar mensaje correspondiente sin opciones. |
| **Historial de acciones** | 0,7 | Mantener historial de las últimas 5 acciones mediante POST (sin sesiones ni cookies). Eliminación de la acción más antigua cuando se superen 5 entradas. |
| **TOTAL EJERCICIO 1** | **2,0** | |

---

## EJERCICIO 2 (RA3 y RA4) - 5 puntos

**Sistema de tienda de videojuegos retro "Pixel Paradise"**

### login.php

| Criterio | Puntuación | Descripción |
|----------|------------|-------------|
| **Formulario de login** | 0,5 | Formulario con campos de usuario y contraseña. Método POST correctamente implementado. |
| **Validación de credenciales** | 0,5 | Array predefinido con al menos 3 usuarios (nombre usuario, contraseña, nombre completo, email, género preferido). Validación correcta de credenciales. |
| **Gestión de sesión** | 0,4 | Inicio de sesión con almacenamiento de toda la información del usuario (nombre completo, email, usuario, género preferido). Redirección a tienda.php si es correcta. Mensaje de error si es incorrecta. |

### tienda.php

| Criterio | Puntuación | Descripción |
|----------|------------|-------------|
| **Control de acceso** | 0,4 | Verificación de sesión activa. Mensaje y redirección automática a login.php tras 3 segundos usando header con refresh si no hay sesión. |
| **Bienvenida y cierre** | 0,4 | Muestra nombre completo del usuario. Botón/enlace de cierre de sesión que destruye la sesión (session_destroy()) y redirige a login.php SIN eliminar cookie de reservas. |
| **Catálogo de videojuegos** | 0,4 | Array con al menos 4 videojuegos (ID, nombre, precio, género, stock). Presentación en tabla o tarjetas con toda la información. |
| **Sistema de recomendaciones** | 0,2 | Etiqueta visual "Recomendado para ti" si el género del juego coincide con la preferencia del usuario en sesión. |
| **Formularios de reserva** | 0,5 | Cada juego con formulario POST conteniendo: checkbox de selección, campo numérico de cantidad (mínimo 1, máximo stock), botón "Añadir a Reserva". |
| **Gestión cookies - añadir** | 1,0 | Validación de selección y cantidades. Cálculo de subtotales. Lectura/creación de cookie "reservas" (deserialización). Añadir juegos con toda la info (ID, nombre, precio, cantidad, subtotal, género). Actualización de cantidades sin exceder stock. Serialización y guardado de cookie (duración 2 horas/7200 segundos). Mensaje de confirmación. |
| **Panel resumen reservas** | 0,7 | Panel que lee cookie "reservas". Muestra: número total de juegos, precio total acumulado, lista detallada (nombre, cantidad, subtotal), botón "Vaciar Reserva" que elimina cookie y recarga. Mensaje "No tienes juegos reservados" si está vacía. |
| **TOTAL EJERCICIO 2** | **5,0** | |

---

## EJERCICIO 3 (RA5) - 3 puntos

**Aplicación CRUD de gestión de hechizos con MySQLi**

**Estructura de la tabla requerida:**
- `id`: clave primaria numérica autoincremental
- `nombre`: nombre del hechizo
- `tipo`: categoría (ataque, defensa o curación)
- `nivel_poder`: valor numérico entre 1 y 100

### agregar.php

| Criterio | Puntuación | Descripción |
|----------|------------|-------------|
| **Formulario** | 0,3 | Formulario con campos para nombre, tipo y nivel_poder. Estructura correcta. |
| **Validación** | 0,2 | Validación de datos: tipo debe ser uno de los permitidos, nivel_poder entre 1 y 100. |
| **Inserción en BD** | 0,5 | Conexión MySQLi correcta. Inserción del hechizo en la base de datos. Mensaje temático de confirmación. |

### listar.php

| Criterio | Puntuación | Descripción |
|----------|------------|-------------|
| **Conexión y consulta** | 0,5 | Conexión MySQLi y consulta SELECT para obtener todos los hechizos. |
| **Presentación de datos** | 0,5 | Tabla o catálogo mostrando claramente todas las propiedades de cada hechizo. |
| **Botón eliminar** | 0,4 | Botón/enlace por cada hechizo que redirija a borrar.php enviando el ID por GET. |

### borrar.php

| Criterio | Puntuación | Descripción |
|----------|------------|-------------|
| **Recepción ID** | 0,2 | Recepción correcta del parámetro ID por GET. |
| **Eliminación BD** | 0,2 | Conexión MySQLi y ejecución de DELETE con el ID recibido. |
| **Mensaje confirmación** | 0,2 | Mensaje indicando que el hechizo ha sido borrado. |
| **TOTAL EJERCICIO 3** | **3,0** | |

---

## RESUMEN DE PUNTUACIÓN

| Ejercicio | Puntuación Máxima |
|-----------|-------------------|
| Ejercicio 1 (RA2) | 2,0 puntos |
| Ejercicio 2 (RA3 y RA4) | 5,0 puntos |
| Ejercicio 3 (RA5) | 3,0 puntos |
| **TOTAL EXAMEN** | **10,0 puntos** |

---

## NOTAS IMPORTANTES

### Instrucciones generales aplicables:
-  Se permite consultar documentación propia y ejercicios resueltos
-  **NO** se permite consultar el proyecto entregado previamente
-  **NO** está permitido el uso de IA (penalización: 0 en el examen)
-  Solo editar archivos .php
-  Si se usa base de datos propia, comentar claramente su contenido

### Entrega:
- **Nombre del archivo:** `DWES1EV_TUNOMBRE.zip`
- **Contenido:** Carpeta con mismo nombre conteniendo estructura completa del proyecto
- Cualquier incumplimiento puede conllevar reducción de nota o calificación de 0

### Detección de IA:
- **Variable trampa:** Si el código está generado por IA, se utilizará una variable llamada `$varProfesorTIA2025`
- En caso de sospecha, se puede requerir explicación por escrito
- Si la explicación no es correcta: **calificación 0**
- Si se pilla al alumno usando IA durante el examen: **calificación 0**

---

## CRITERIOS DE EVALUACIÓN POR RESULTADOS DE APRENDIZAJE

### RA2 - Escribe sentencias en lenguaje de marcas para generar páginas web dinámicas
- Ejercicio 1: Formularios, procesamiento POST, lógica PHP, control de flujo

### RA3 - Escribe sentencias de lenguaje embebido en páginas web para gestionar información almacenada en sesiones
- Ejercicio 2 (login.php y control de sesiones en tienda.php)

### RA4 - Escribe sentencias de lenguaje embebido en páginas web para gestionar información almacenada en cookies
- Ejercicio 2 (gestión de cookies en tienda.php)

### RA5 - Desarrolla aplicaciones web embebidas en lenguajes de marcas, analizando e incorporando funcionalidades según especificaciones
- Ejercicio 3: Operaciones CRUD con MySQLi, conexión a base de datos, consultas SQL
