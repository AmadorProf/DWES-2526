-- Crear base de datos
CREATE DATABASE IF NOT EXISTS biblioteca_magica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE biblioteca_magica;

-- Crear tabla de hechizos
CREATE TABLE IF NOT EXISTS hechizos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('ataque', 'defensa', 'curación') NOT NULL,
    nivel_poder INT NOT NULL CHECK (nivel_poder BETWEEN 1 AND 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar algunos hechizos de ejemplo
INSERT INTO hechizos (nombre, tipo, nivel_poder) VALUES
('Bola de Fuego', 'ataque', 85),
('Escudo Arcano', 'defensa', 70),
('Curacion Divina', 'curación', 90),
('Rayo Oscuro', 'ataque', 95),
('Barrera Magica', 'defensa', 60);
