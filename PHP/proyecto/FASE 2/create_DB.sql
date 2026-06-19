-- Base de datos para MountainConnect
-- Fase 2: Integración con MySQL

CREATE DATABASE IF NOT EXISTS mountain_connect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mountain_connect;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nivel_experiencia ENUM('principiante', 'intermedio', 'avanzado', 'experto') NOT NULL,
    especialidad ENUM('senderismo', 'escalada', 'ferratas', 'alpinismo', 'trail') DEFAULT NULL,
    provincia VARCHAR(50) NOT NULL,
    foto_perfil VARCHAR(255) DEFAULT NULL,
    biografia TEXT DEFAULT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL DEFAULT NULL,
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de rutas
CREATE TABLE rutas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    dificultad ENUM('facil', 'moderada', 'dificil', 'muy_dificil') NOT NULL,
    distancia DECIMAL(6,2) NOT NULL,
    desnivel INT NOT NULL,
    duracion DECIMAL(4,1) NOT NULL,
    provincia VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    nivel_tecnico TINYINT NOT NULL CHECK (nivel_tecnico BETWEEN 1 AND 5),
    nivel_fisico TINYINT NOT NULL CHECK (nivel_fisico BETWEEN 1 AND 5),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    visitas INT DEFAULT 0,
    likes INT DEFAULT 0,
    activa BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_dificultad (dificultad),
    INDEX idx_provincia (provincia),
    INDEX idx_user_id (user_id),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de épocas recomendadas para rutas
CREATE TABLE rutas_epocas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ruta_id INT NOT NULL,
    epoca ENUM('primavera', 'verano', 'otono', 'invierno') NOT NULL,
    FOREIGN KEY (ruta_id) REFERENCES rutas(id) ON DELETE CASCADE,
    UNIQUE KEY unique_ruta_epoca (ruta_id, epoca)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de fotografías
CREATE TABLE fotografias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ruta_id INT NOT NULL,
    user_id INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    titulo VARCHAR(100) DEFAULT NULL,
    descripcion TEXT DEFAULT NULL,
    orden TINYINT DEFAULT 0,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ruta_id) REFERENCES rutas(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_ruta_id (ruta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de vías ferratas
CREATE TABLE ferratas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    dificultad ENUM('K1', 'K2', 'K3', 'K4', 'K5', 'K6') NOT NULL,
    desnivel INT NOT NULL,
    duracion DECIMAL(4,1) NOT NULL,
    provincia VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    equipamiento TEXT DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de vías de escalada
CREATE TABLE escalada (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    grado VARCHAR(10) NOT NULL,
    tipo ENUM('deportiva', 'clasica', 'boulder', 'artificial') NOT NULL,
    longitud INT NOT NULL,
    numero_seguros INT DEFAULT NULL,
    provincia VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de comentarios
CREATE TABLE comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ruta_id INT NOT NULL,
    comentario TEXT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (ruta_id) REFERENCES rutas(id) ON DELETE CASCADE,
    INDEX idx_ruta_id (ruta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de likes/favoritos
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ruta_id INT NOT NULL,
    fecha_like TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (ruta_id) REFERENCES rutas(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_ruta (user_id, ruta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de seguidores (usuarios que siguen a otros)
CREATE TABLE seguidores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seguidor_id INT NOT NULL,
    seguido_id INT NOT NULL,
    fecha_seguimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seguidor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (seguido_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_seguimiento (seguidor_id, seguido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos de ejemplo
INSERT INTO usuarios (username, email, password, nivel_experiencia, especialidad, provincia) VALUES
('montanero_pro', 'montanero@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'experto', 'senderismo', 'Huesca'),
('escalador_23', 'escalador@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'avanzado', 'escalada', 'Granada');

-- Nota: La contraseña de ejemplo es 'password' (usar password_hash en producción)

-- Vistas útiles para consultas

-- Vista de rutas con información del usuario
CREATE VIEW vista_rutas_completas AS
SELECT 
    r.*,
    u.username,
    u.nivel_experiencia as user_nivel,
    COUNT(DISTINCT f.id) as num_fotos,
    COUNT(DISTINCT c.id) as num_comentarios,
    COUNT(DISTINCT l.id) as num_likes
FROM rutas r
JOIN usuarios u ON r.user_id = u.id
LEFT JOIN fotografias f ON r.id = f.ruta_id
LEFT JOIN comentarios c ON r.id = c.ruta_id
LEFT JOIN likes l ON r.id = l.ruta_id
WHERE r.activa = TRUE
GROUP BY r.id;

-- Procedimientos almacenados (opcional para Fase 3)

DELIMITER //

-- Procedimiento para incrementar visitas
CREATE PROCEDURE incrementar_visitas(IN ruta_id_param INT)
BEGIN
    UPDATE rutas SET visitas = visitas + 1 WHERE id = ruta_id_param;
END //

-- Procedimiento para obtener estadísticas de usuario
CREATE PROCEDURE estadisticas_usuario(IN user_id_param INT)
BEGIN
    SELECT 
        COUNT(DISTINCT r.id) as total_rutas,
        COUNT(DISTINCT f.id) as total_fotos,
        COUNT(DISTINCT c.id) as total_comentarios,
        SUM(r.likes) as total_likes_recibidos
    FROM usuarios u
    LEFT JOIN rutas r ON u.id = r.user_id
    LEFT JOIN fotografias f ON u.id = f.user_id
    LEFT JOIN comentarios c ON u.id = c.user_id
    WHERE u.id = user_id_param;
END //

DELIMITER ;

-- Triggers de ejemplo

DELIMITER //

-- Trigger para actualizar ultimo_acceso al hacer login
CREATE TRIGGER actualizar_ultimo_acceso
BEFORE UPDATE ON usuarios
FOR EACH ROW
BEGIN
    IF NEW.ultimo_acceso IS NULL OR NEW.ultimo_acceso < OLD.ultimo_acceso THEN
        SET NEW.ultimo_acceso = CURRENT_TIMESTAMP;
    END IF;
END //

DELIMITER ;