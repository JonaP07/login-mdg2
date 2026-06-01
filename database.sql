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
-- Hash real verificado para '1234'
CREATE TABLE clientes (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    creado_en TIMESTAMP DEFAULT NOW()
);
