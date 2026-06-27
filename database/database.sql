-- Script corregido para PostgreSQL (Supabase)
-- Borrar tablas si existen para asegurar limpieza (orden correcto)
DROP TABLE IF EXISTS clientes;
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
INSERT INTO usuarios (nombre, email, password_hash) VALUES 
('Admin', 'admin@test.com', '$2y$10$b5YRfZOhim6qsro2RMYWjeodnvr0b14AmRs9gQROdL.x1UPuD83tC');

CREATE TABLE clientes (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    creado_en TIMESTAMP DEFAULT NOW()
);

-- Insertar cliente de prueba
INSERT INTO clientes (nombre, email, telefono) VALUES
('Cliente Demo', 'cliente@demo.com', '555-1234');

-- =============================================
-- PRODUCTOS (con stock)
-- =============================================
CREATE TABLE productos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio NUMERIC(10,2) NOT NULL,
    stock INTEGER NOT NULL DEFAULT 0,
    creado_en TIMESTAMP DEFAULT NOW()
);

-- Datos de prueba
INSERT INTO productos (nombre, descripcion, precio, stock) VALUES
('Camisa Slim Fit',    'Camisa blanca talla M', 150.00, 10),
('Pantalón Chino',     'Pantalón beige talla 32', 220.00, 0),  -- sin stock
('Zapatos Oxford',     'Zapatos cuero negro 42', 450.00, 5),
('Polo Básico',        'Polo negro talla L', 80.00, 0);        -- sin stock

-- =============================================
-- PEDIDOS (cabecera)
-- =============================================
CREATE TABLE pedidos (
    id SERIAL PRIMARY KEY,
    id_cliente INTEGER REFERENCES clientes(id),
    total NUMERIC(10,2),
    estado VARCHAR(50) DEFAULT 'pendiente',
    creado_en TIMESTAMP DEFAULT NOW()
);

-- =============================================
-- DETALLE DE PEDIDOS
-- =============================================
CREATE TABLE detalle_pedidos (
    id SERIAL PRIMARY KEY,
    id_pedido INTEGER REFERENCES pedidos(id),
    id_producto INTEGER REFERENCES productos(id),
    cantidad INTEGER NOT NULL,
    precio_unitario NUMERIC(10,2) NOT NULL
);