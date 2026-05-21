<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

$pdo = getDbConnection();

$pdo->exec("CREATE TABLE IF NOT EXISTS reportes (
    id SERIAL PRIMARY KEY,
    correo VARCHAR(255) NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

jsonResponse(["mensaje" => "Tabla reportes creada"]);
