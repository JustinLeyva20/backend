CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    correo VARCHAR(255) UNIQUE NOT NULL,
    pass VARCHAR(255) NOT NULL,
    telefono VARCHAR(50) DEFAULT '',
    direccion TEXT DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS menu_items (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    imagen TEXT DEFAULT '',
    badge VARCHAR(100) DEFAULT '',
    categoria VARCHAR(50) NOT NULL,
    fecha VARCHAR(100) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pedidos (
    id SERIAL PRIMARY KEY,
    usuario VARCHAR(255) NOT NULL,
    nombre_cliente VARCHAR(255) NOT NULL,
    telefono VARCHAR(50) NOT NULL,
    direccion TEXT NOT NULL,
    fecha VARCHAR(50) NOT NULL,
    hora VARCHAR(50) NOT NULL,
    metodo_pago VARCHAR(50) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    estado VARCHAR(50) DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS detalle_pedidos (
    id SERIAL PRIMARY KEY,
    pedido_id INTEGER REFERENCES pedidos(id) ON DELETE CASCADE,
    nombre VARCHAR(255) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    cantidad INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS configuracion (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    ruc VARCHAR(50) NOT NULL,
    telefono VARCHAR(50) NOT NULL,
    direccion TEXT NOT NULL,
    horario_apertura VARCHAR(50) NOT NULL,
    horario_cierre VARCHAR(50) NOT NULL
);

INSERT INTO configuracion (nombre, ruc, telefono, direccion, horario_apertura, horario_cierre)
VALUES ('Mi Restaurante', '12345678901', '999888777', 'Av. Principal 123', '08:00', '22:00')
ON CONFLICT DO NOTHING;

CREATE TABLE IF NOT EXISTS salas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    mesas INTEGER NOT NULL DEFAULT 8
);

INSERT INTO salas (nombre, mesas) VALUES ('Salón Principal', 8)
ON CONFLICT DO NOTHING;
INSERT INTO salas (nombre, mesas) VALUES ('Terraza', 6)
ON CONFLICT DO NOTHING;
INSERT INTO salas (nombre, mesas) VALUES ('VIP', 4)
ON CONFLICT DO NOTHING;
INSERT INTO salas (nombre, mesas) VALUES ('Jardín', 6)
ON CONFLICT DO NOTHING;

CREATE TABLE IF NOT EXISTS reservas (
    id SERIAL PRIMARY KEY,
    id_sala INTEGER NOT NULL REFERENCES salas(id) ON DELETE CASCADE,
    num_mesa INTEGER NOT NULL,
    usuario VARCHAR(255) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    personas INTEGER NOT NULL DEFAULT 1,
    nota TEXT DEFAULT '',
    estado VARCHAR(50) DEFAULT 'PENDIENTE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
