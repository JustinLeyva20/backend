<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

$pdo = getDbConnection();

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS salas (
            id SERIAL PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            mesas INTEGER NOT NULL DEFAULT 8
        )
    ");

    $pdo->exec("
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
        )
    ");

    $count = $pdo->query("SELECT COUNT(*) as c FROM salas")->fetch()['c'];
    if ($count == 0) {
        $pdo->exec("INSERT INTO salas (nombre, mesas) VALUES ('Salón Principal', 8)");
        $pdo->exec("INSERT INTO salas (nombre, mesas) VALUES ('Terraza', 6)");
        $pdo->exec("INSERT INTO salas (nombre, mesas) VALUES ('VIP', 4)");
        $pdo->exec("INSERT INTO salas (nombre, mesas) VALUES ('Jardín', 6)");
    }

    jsonResponse(["mensaje" => "Tablas creadas correctamente", "salas_creadas" => $count == 0 ? 4 : 0]);
} catch (Exception $e) {
    jsonResponse(["error" => $e->getMessage()], 500);
}
