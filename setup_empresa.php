<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

$pdo = getDbConnection();

try {
    $pdo->exec("ALTER TABLE configuracion ADD COLUMN IF NOT EXISTS mensaje TEXT DEFAULT ''");
} catch (PDOException $e) {
    // Column may already exist
}

$stmt = $pdo->query("SELECT COUNT(*) as cnt FROM configuracion");
$row = $stmt->fetch();

if ((int)$row['cnt'] === 0) {
    $pdo->exec("INSERT INTO configuracion (nombre, ruc, telefono, direccion, horario_apertura, horario_cierre, mensaje) VALUES ('Restaurante la Delicia', '65479877', '957847894', 'Lima - Perú', '08:00', '20:00', 'Gracias por su visita')");
} else {
    $pdo->exec("UPDATE configuracion SET nombre = 'Restaurante la Delicia', ruc = '65479877', telefono = '957847894', direccion = 'Lima - Perú', horario_apertura = '08:00', horario_cierre = '20:00', mensaje = 'Gracias por su visita' WHERE id = (SELECT id FROM configuracion ORDER BY id LIMIT 1)");
}

jsonResponse(["mensaje" => "Configuración actualizada"]);
