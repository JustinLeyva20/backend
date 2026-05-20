<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(["error" => "Método no permitido"], 405);
}

$pdo = getDbConnection();
$stmt = $pdo->query("SELECT nombre, ruc, telefono, direccion, horario_apertura, horario_cierre FROM configuracion ORDER BY id DESC LIMIT 1");
$config = $stmt->fetch();

if (!$config) {
    jsonResponse(["error" => "Configuración no encontrada"], 404);
}

jsonResponse([
    "nombre" => $config['nombre'],
    "ruc" => $config['ruc'],
    "telefono" => $config['telefono'],
    "direccion" => $config['direccion'],
    "horario_apertura" => $config['horario_apertura'],
    "horario_cierre" => $config['horario_cierre'],
]);
