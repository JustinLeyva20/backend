<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["error" => "Método no permitido"], 405);
}

$input = getJsonInput();

$id_sala  = (int)($input['id_sala'] ?? 0);
$num_mesa = (int)($input['num_mesa'] ?? 0);
$usuario  = trim($input['usuario'] ?? '');
$fecha    = trim($input['fecha'] ?? '');
$hora     = trim($input['hora'] ?? '');
$personas = (int)($input['personas'] ?? 1);
$nota     = trim($input['nota'] ?? '');

if (!$id_sala || !$num_mesa || !$usuario || !$fecha || !$hora || $personas < 1) {
    jsonResponse(["error" => "Completa todos los campos obligatorios"], 400);
}

$pdo = getDbConnection();

$stmt = $pdo->prepare(
    "SELECT COUNT(*) as c FROM reservas
     WHERE id_sala = ? AND num_mesa = ? AND fecha = ? AND estado = 'PENDIENTE'"
);
$stmt->execute([$id_sala, $num_mesa, $fecha]);
$ocupada = $stmt->fetch()['c'] > 0;

if ($ocupada) {
    jsonResponse(["error" => "Esa mesa ya tiene una reserva activa para esa fecha"], 409);
}

$stmt = $pdo->prepare(
    "INSERT INTO reservas (id_sala, num_mesa, usuario, fecha, hora, personas, nota)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->execute([$id_sala, $num_mesa, $usuario, $fecha, $hora, $personas, $nota]);

$id = $pdo->lastInsertId();

jsonResponse([
    "mensaje" => "Reserva confirmada",
    "id" => (int)$id,
    "id_sala" => $id_sala,
    "num_mesa" => $num_mesa,
    "fecha" => $fecha,
    "hora" => $hora,
    "personas" => $personas,
    "nota" => $nota,
    "estado" => "PENDIENTE",
], 201);
