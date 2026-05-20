<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(["error" => "Método no permitido"], 405);
}

$pdo = getDbConnection();
$stmt = $pdo->query("SELECT id, nombre, precio, COALESCE(imagen, '') as imagen, fecha FROM platos ORDER BY id");
$items = $stmt->fetchAll();

$items = array_map(function ($item) {
    return [
        "id" => (int)$item['id'],
        "nombre" => $item['nombre'],
        "precio" => (float)$item['precio'],
        "imagen" => $item['imagen'],
        "badge" => "",
        "fecha" => $item['fecha'] ?? '',
    ];
}, $items);

jsonResponse($items);
