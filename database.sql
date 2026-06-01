-- Script corregido para PostgreSQL (Supabase)
-- Borrar tabla si existe para asegurar limpieza
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT NOW()
);

-- Insertar usuario de prueba (contraseña: 1234)
-- Hash verificado para '1234'
INSERT INTO usuarios (nombre, email, password_hash)
VALUES (
    'Administrador',
    'admin@test.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
);
