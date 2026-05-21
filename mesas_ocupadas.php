<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(["error" => "Método no permitido"], 405);
}

$fecha = trim($_GET['fecha'] ?? date('Y-m-d'));

$pdo = getDbConnection();

$stmt = $pdo->prepare(
    "SELECT id_sala, num_mesa FROM reservas WHERE estado = 'PENDIENTE' AND fecha = ?"
);
$stmt->execute([$fecha]);
$ocupadas = $stmt->fetchAll();

$result = [];
foreach ($ocupadas as $o) {
    $result[] = [
        "id_sala" => (int)$o['id_sala'],
        "num_mesa" => (int)$o['num_mesa'],
    ];
}

jsonResponse($result);
