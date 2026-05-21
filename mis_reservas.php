<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(["error" => "Método no permitido"], 405);
}

$usuario = trim($_GET['usuario'] ?? '');

if (empty($usuario)) {
    jsonResponse(["error" => "Parámetro usuario requerido"], 400);
}

$pdo = getDbConnection();

$stmt = $pdo->prepare(
    "SELECT r.id, r.id_sala, s.nombre as sala_nombre, r.num_mesa, r.usuario,
            r.fecha, r.hora, r.personas, r.nota, r.estado, r.created_at
     FROM reservas r
     JOIN salas s ON s.id = r.id_sala
     WHERE r.usuario = ?
     ORDER BY r.fecha DESC, r.hora DESC"
);
$stmt->execute([$usuario]);
$reservas = $stmt->fetchAll();

$result = array_map(function ($r) {
    return [
        "id" => (int)$r['id'],
        "id_sala" => (int)$r['id_sala'],
        "sala_nombre" => $r['sala_nombre'],
        "num_mesa" => (int)$r['num_mesa'],
        "usuario" => $r['usuario'],
        "fecha" => $r['fecha'],
        "hora" => substr($r['hora'], 0, 5),
        "personas" => (int)$r['personas'],
        "nota" => $r['nota'],
        "estado" => $r['estado'],
        "created_at" => $r['created_at'],
    ];
}, $reservas);

jsonResponse($result);
